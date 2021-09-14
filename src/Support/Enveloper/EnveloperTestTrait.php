<?php

namespace Mashbo\CoreTesting\Support\Enveloper;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

trait EnveloperTestTrait
{
    private $client;

    /**
     * @before
     */
    public function setupEnveloperBeforeTest(): void
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));

        $this->client = new Client([
            'base_uri' => getenv('ENVELOPER_API'),
            'handler' => $handlerStack
        ]);
        $this->reset();
    }

    private function retryDecider(): callable
    {
        return static function (
            int $retries,
            Request $request,
            Response $response = null,
            TransferException $exception = null
        ) {
            // Limit the number of retries to 5
            if ($retries >= 5) {
                return false;
            }

            // Retry connection exceptions
            if ($exception instanceof ConnectException) {
                return true;
            }

            if ($response && in_array($response->getStatusCode(), [502, 503, 504], true)) {
                // Retry on server errors
                return true;
            }

            return false;
        };
    }

    /**
     * delay 1s 2s 3s 4s 5s
     *
     * @return Closure
     */
    private function retryDelay(): callable
    {
        return static function ($numberOfRetries) {
            return 500 * $numberOfRetries;
        };
    }

    private function matchAllCallables(array $callables): callable
    {
        return static function () use ($callables) {
            $args = func_get_args();
            foreach ($callables as $index => $callable) {
                if (!$callable(...$args)) {
                    return false;
                }
            }
            return true;
        };
    }

    protected function assertEmailRequestSent(...$matchers): void
    {
        $callable = $this->matchAllCallables($matchers);
        $messages = json_decode($this->client->get('outbox')->getBody())->items;
        foreach ($messages as $item) {
            if ($callable($item)) {
                return;
            }
        }

        throw new \LogicException('No matching email request sent of ' . count($messages) . ' messages');
    }

    protected function assertEmailRequestNotSent(...$matchers): void
    {
        $callable = $this->matchAllCallables($matchers);
        $messages = json_decode($this->client->get('outbox')->getBody())->items;
        foreach ($messages as $item) {
            if ($callable($item)) {
                throw new \LogicException('Matching email request sent but was not expected');
            }
        }
    }

    protected function toContains(string $emailAddress): callable
    {
        return static function (\stdClass $request) use ($emailAddress): bool {
            return strtolower($request->parameters->to) === strtolower($emailAddress);
        };
    }

    protected function templateIs(string $template): callable
    {
        return static function (\stdClass $request) use ($template): bool {
            return $request->template === $template;
        };
    }

    protected function includesAttachmentNamed(string $filename): callable
    {
        return static function (\stdClass $request) use ($filename): bool {
            foreach ($request->parameters->attachments as $attachment) {
                if ($attachment->filename === $filename) {
                    return true;
                }
            }
            return false;
        };
    }

    protected function assertTemplateSentTo(string $template, $emailAddress): void
    {
        $this->assertEmailRequestSent(
            $this->templateIs($template),
            $this->toContains($emailAddress)
        );
    }

    private function assertMatchingEmailSent($callable)
    {
        sleep(3);
        $messages = json_decode($this->client->get('outbox')->getBody());
        foreach ($messages as $item) {
            if ($callable($item)) {
                return;
            }
        }

        throw new \LogicException("No matching email sent of " . count($messages) . " messages");
    }

    private function assertOnlyOneMatchingEmailSent($callable)
    {
        $this->assertMatchingEmailSent($callable);
        $messages = json_decode($this->client->get('outbox')->getBody());

        $matchingMessages = array_filter($messages, function ($message) use ($callable) {
            return $callable($message);
        });

        if (count($matchingMessages) == 1) {
            return;
        }

        throw new \LogicException(sprintf(
            '%d matching messages of %d message sent. 1 expected',
            count($matchingMessages),
            count($messages)
        ));
    }

    private function assertNoMatchingEmailSent($callable)
    {
        try {
            $this->assertMatchingEmailSent($callable);
        } catch (\LogicException $exception) {
            return;
        }

        $messages = json_decode($this->client->get('outbox')->getBody());

        throw new \LogicException("Matching email sent of " . count($messages) . " messages");
    }

    protected function assertNoEmailsSent()
    {
        $count = count(json_decode($this->client->get('outbox')->getBody()));
        if (0 !== $count) {
            throw new \LogicException("Expected no messages, found $count");
        }
    }

    protected function findFirstEmailMatching(...$matchers)
    {
        $callable = $this->matchAllCallables($matchers);
        foreach (json_decode($this->client->get('outbox')->getBody())->items as $item) {
            if ($callable($item)) {
                return $item;
            }
        }
    }

    protected function reset()
    {
        $this->client->delete('outbox');
    }
}
