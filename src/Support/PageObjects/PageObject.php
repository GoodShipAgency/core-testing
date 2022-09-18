<?php

declare(strict_types=1);

namespace Mashbo\CoreTesting\Support\PageObjects;

use Mashbo\CoreTesting\Support\ElementObjects\FormElement;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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

    /**
     * @param array<string, string|array> $args
     */
    public function submitSlideoutForm(string $selector, string $prefix, array $args = []): static
    {
        $currentUri = $this->browser->getHistory()->current()->getUri();

        $this->clickLink($selector);

        $this->browser->submitForm(
            $prefix . '_submit',
            FormElement::prefixFormValues($args, $prefix)
        );

        $this->browser->request('GET', $currentUri);

        return $this;
    }

    public function debug(): static
    {
        if (!$this->browser instanceof KernelBrowser) {
            return $this;
        }

        echo $this->browser->getResponse()->getContent();

        return $this;
    }
}
