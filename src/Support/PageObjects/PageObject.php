<?php

declare(strict_types=1);

namespace Mashbo\CoreTesting\Support\PageObjects;

use Symfony\Component\BrowserKit\AbstractBrowser;

abstract class PageObject
{
    protected AbstractBrowser $browser;
    protected string $path = '';

    public function __construct(AbstractBrowser $client)
    {
        $this->browser = $client;
    }

    protected function getBrowser(): AbstractBrowser
    {
        return $this->browser;
    }

    public function clickLink(string $selector): self
    {
        $this->browser->click(
            $this->browser->getCrawler()->filter($selector)->link()
        );

        return $this;
    }
}
