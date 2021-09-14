<?php

namespace Mashbo\CoreTesting\Support\ElementObjects;

use PHPUnit\Framework\Assert;
use Symfony\Component\DomCrawler\Crawler;

class PaginationControlElement
{
    private Crawler $crawler;
    private string $selector;

    public function __construct(Crawler $crawler, string $selector)
    {
        $this->crawler = $crawler;
        $this->selector = $selector;
    }

    public function assertRange(int $first, int $last): void
    {
        Assert::assertMatchesRegularExpression(
            "/^Showing $first to $last of \d+ results?$/",
            $this->crawler->filter("{$this->selector} .pagination__summary")->text()
        );
    }

    public function isPreviousAvailable(): bool
    {
        return $this->crawler->filter("{$this->selector} .pagination__previous_btn")->count() === 1;
    }

    public function isNextAvailable(): bool
    {
        return $this->crawler->filter("{$this->selector} .pagination__next_btn")->count() === 1;
    }

    public function getNextLink(): Crawler
    {
        return $this->crawler->filter("{$this->selector} .pagination__next_btn");
    }

    public function getPreviousLink(): Crawler
    {
        return $this->crawler->filter("{$this->selector} .pagination__previous_btn");
    }
}
