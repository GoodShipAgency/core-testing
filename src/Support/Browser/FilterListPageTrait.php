<?php

namespace Mashbo\CoreTesting\Support\Browser;

use Mashbo\CoreTesting\Support\ElementObjects\FormElement;
use Mashbo\CoreTesting\Support\ElementObjects\ListElement;
use PHPUnit\Framework\Assert;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Crawler;

trait FilterListPageTrait
{
    abstract protected function getBrowser(): AbstractBrowser;

    protected function getSubmitButtonId(): string {
        return $this->getFormPrefix() . '_submit';
    }

    protected function getFormPrefix(): string {
        return '';
    }

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

    public function assertFilterTextEquals(string $filter, string $expectedText): self
    {
        Assert::assertEquals($expectedText, $this->getFilterTab($filter)->text());

        return $this;
    }

    private function getFilterTokenElement(): Crawler
    {
        return $this->getBrowser()->getCrawler()->filterXPath(sprintf("//button[@value='_token']"));
    }

    private function getActiveFiltersElement(): Crawler
    {
        return $this->getBrowser()->getCrawler()->filterXPath("//div[contains(@class, 'active-filters')]");
    }

    protected function getFilterTab(string $filterName): Crawler
    {
        return $this->getBrowser()->getCrawler()->filterXPath(sprintf("//div[@data-row-id='%s_tab']", $filterName))->first();
    }
}
