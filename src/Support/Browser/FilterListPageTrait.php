<?php

namespace Mashbo\CoreTesting\Support\Browser;

use Mashbo\CoreTesting\Support\ElementObjects\ListElement;
use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Crawler;

trait FilterListPageTrait
{
    abstract protected function getBrowser(): AbstractBrowser;

    abstract protected function getSubmitButtonId(): string;

    abstract protected function getFormPrefix(): string;

    public function assertCount(int $expectedCount): static
    {
        $this
            ->getListElement()
            ->assertCount($expectedCount);
        return $this;
    }

    public function filter(array $args): static
    {
        $this->getBrowser()->submitForm(
            $this->getSubmitButtonId(),
            FormElement::prefixFormValues($args, $this->getFormPrefix()),
            'GET'
        );
        return $this;
    }

    private function getListElement(): ListElement
    {
        return (new ListElement($this->getBrowser()->getCrawler(), '.items', '.item'));
    }

    public function assertHasNoActiveFilters(): static
    {
        Assert::assertCount(0, $this->getActiveFiltersElement());

        return $this;
    }

    public function assertHasActiveFilters(): static
    {
        Assert::assertCount(1, $this->getActiveFiltersElement());

        return $this;
    }

    public function assertHasNoTokenFilter(): static
    {
        Assert::assertCount(0, $this->getFilterTokenElement());

        return $this;
    }

    public function assertFilterTextEquals(string $filter, string $expectedText, string $prefix = 'filter_cases'): self
    {
        // When the filter form is refactored to be more generic, filter_cases value should reflect new prefix
        Assert::assertEquals($expectedText, $this->getFilter($prefix, $filter)->text());

        return $this;
    }

    private function getFilterTokenElement(): Crawler
    {
        return $this->getBrowser()->getCrawler()->filterXPath(sprintf("//button[@value='%s__token']", $this->getFormPrefix()));
    }

    private function getActiveFiltersElement(): Crawler
    {
        return $this->getBrowser()->getCrawler()->filterXPath("//div[contains(@class, 'active-filters')]");
    }

    protected function getFilter(string $prefix, string $filterName): Crawler
    {
        return $this->getBrowser()->getCrawler()->filterXPath(sprintf("//div[@data-row-id='%s_%s']", $prefix, $filterName))->first();
    }
}
