<?php

namespace Mashbo\CoreTesting\Support\Browser;

use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ExportTrait
{
    abstract protected function getBrowser(): AbstractBrowser;

    /**
     * We need to use output buffering since a StreamedResponse uses echo
     * so this ends up in the terminal.
     *
     * We capture output from the CLI, detect StreamedResponses and convert
     * to regular Response objects first
     */
    public function export(): Response
    {
        ob_start();
        $this->getBrowser()->click($this->getBrowser()->getCrawler()->filter('.btn.export')->link());
        $response = $this->getBrowser()->getResponse();

        if ($response instanceof StreamedResponse) {
            $response = new Response(ob_get_clean(), $response->getStatusCode(), $response->headers->all());
        }

        if ($response instanceof Response) {
            return $response;
        }

        throw new \LogicException("Underlying response was not a HttpFoundation response");
    }

    public static function assertResponseContentType(Response $response, string $expectedContentType): void
    {
        Assert::assertSame($expectedContentType, $response->headers->get('Content-type'));
    }

    public static function assertResponseContentTypeIsCSV(Response $response): void
    {
        static::assertResponseContentType($response, 'text/csv; charset=UTF-8');
    }

    public static function assertContentDispositionAttachment(Response $response, string $filename): void
    {
        Assert::assertSame(
            sprintf('attachment; filename=%s', $filename),
            $response->headers->get('Content-disposition')
        );
    }
}
