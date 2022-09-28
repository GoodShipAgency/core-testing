<?php

namespace Mashbo\CoreTesting\Support\Browser;

use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ExportTrait
{
    use StreamedResponseTrait;

    abstract protected function getBrowser(): AbstractBrowser;

    public function export(): Response
    {
        $link = $this->getBrowser()->getCrawler()->filter('.btn.export')->link();
        return $this->exportByLink($link);
    }

    /**
     * We detect StreamedResponses and convert to regular Response objects first
     */
    public function exportByLink(Link $link): Response
    {
        $this->getBrowser()->click($link);
        return $this->filterStreamedResponse($this->getBrowser()->getResponse());
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
