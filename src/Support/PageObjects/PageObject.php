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


    public function debug(?string $selector = null, bool $includeParent = true): static
    {
        if (!$this->browser instanceof KernelBrowser) {
            return $this;
        }

        if ($selector === null) {
            $this->outputHtml($this->browser->getResponse()->getContent() ?: 'No content');
        } else {
            $node = $this->browser->getCrawler()->filter($selector);

            if ($includeParent) {
                $node = $node->ancestors()->first();
            }
            $this->outputHtml($node->html());
        }
        return $this;
    }

    /**
     * @psalm-suppress all
     */
    private function outputHtml(string $html): void
    {
        if (!class_exists('\tidy')) {
            echo $html;
            return;
        }

        $tidy = new \tidy;
        $tidy->parseString($html, [
            'indent' => true,
            'output-xhtml' => true,
            'wrap' => 200
        ]);

        $tidy->cleanRepair();

        /** @psalm-suppress InvalidArgument */
        echo $tidy;
    }

    /**
     * @template T of PageObject
     *
     * @param class-string<T> $pageClass
     * @psalm-param class-string<T> $pageClass
     *
     * @return T
     * @psalm-return T
     */
    public function followRedirectToPage(string $pageClass): self
    {
        $this->browser->followRedirect();

        /** @psalm-suppress UnsafeInstantiation */
        return new $pageClass($this->browser);
    }
}
