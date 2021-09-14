<?php

namespace Mashbo\CoreTesting\Support\Browser;

use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\AbstractBrowser;

trait SecurityPageTrait
{
    abstract protected function getBrowser(): AbstractBrowser;

    public function assertAccessDenied(): static
    {
        Assert::assertEquals(403, $this->getBrowser()->getResponse()->getStatusCode());

        return $this;
    }

    public function assertSuccessful(): static
    {
        /** @var int $responseCode */
        $responseCode = $this->getBrowser()->getResponse()->getStatusCode();

        Assert::assertGreaterThanOrEqual(200, $responseCode);
        Assert::assertLessThan(300, $responseCode);

        return $this;
    }
}
