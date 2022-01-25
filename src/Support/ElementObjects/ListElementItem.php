<?php

namespace Mashbo\CoreTesting\Support\ElementObjects;

use Symfony\Component\DomCrawler\Crawler;
use Webmozart\Assert\Assert;

class ListElementItem
{
    public function __construct(private Crawler $crawler)
    {
    }

    public function assertBadge(string $expectedText): self
    {
        foreach ($this->crawler->filter('.badge') as $badge) {
            if ($expectedText === trim($badge->textContent)) {
                Assert::true(true);
                return $this;
            }
        }
        throw new \LogicException("No badge found in page with text $expectedText");
    }

    public function getCrawler(): Crawler
    {
        return $this->crawler;
    }
}
