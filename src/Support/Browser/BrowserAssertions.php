<?php

namespace Mashbo\CoreTesting\Support\Browser;

use Mashbo\CoreTesting\Support\Browser\Exceptions\UnexpectedStatusCodeException;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\AbstractBrowser;

class BrowserAssertions
{
    public static function assertPageLoadedSuccessfully(AbstractBrowser $browser): void
    {
        if ($browser instanceof KernelBrowser && !$browser->getResponse()->isSuccessful()) {
            throw new UnexpectedStatusCodeException($browser->getRequest(), $browser->getResponse(), "20x");
        }
    }

    public static function assertPageIs(AbstractBrowser $browser, string $string): void
    {
        $path = self::getBasePath($browser);
        Assert::assertSame($string, $path, "Expected path to be $string, found $path. Did you forget to open() the page or click() a link?");
    }

    public static function assertPageMatches(AbstractBrowser $browser, string $string): void
    {
        $path = self::getBasePath($browser);
        Assert::assertMatchesRegularExpression('@' . $string . '@', $path, "Expected path to match $string, found $path. Did the page not redirect as expected?");
    }

    private static function getBasePath(AbstractBrowser $browser): string
    {
        $base = "http://{$browser->getServerParameter('HTTP_HOST')}";
        return str_replace($base, '', $browser->getHistory()->current()->getUri());
    }
}
