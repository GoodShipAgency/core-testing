<?php

namespace Mashbo\CoreTesting\Support\Browser;

use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait StreamedResponseTrait
{
    abstract protected function getBrowser(): AbstractBrowser;

    protected function filterStreamedResponse(object $response): Response
    {
        if ($response instanceof StreamedResponse) {
            // We can't use ob_start / ob_get_clean since 6.1 because of this bug:
            // https://github.com/symfony/symfony/issues/46445
            // We can access the content of the response via the
            // client's internal response though
            $internalResponse = $this->getBrowser()->getInternalResponse();
            $response = new Response(
                $internalResponse->getContent(),
                $response->getStatusCode(),
                $response->headers->all()
            );
        }

        if ($response instanceof Response) {
            return $response;
        }

        throw new \LogicException(sprintf('A %s was expected.', Response::class));
    }
}
