<?php

namespace Mashbo\CoreTesting\Support\ElementObjects;

use PHPUnit\Framework\Assert;
use Symfony\Component\DomCrawler\Crawler;

class ListElement implements \IteratorAggregate
{
    private Crawler $crawler;
    private string $rootSelector;
    private string $itemSelector;

    public function __construct(Crawler $crawler, string $rootSelector, string $itemSelector)
    {
        $this->crawler = $crawler;
        $this->rootSelector = $rootSelector;
        $this->itemSelector = $itemSelector;
    }

    private function iterator(): \Generator
    {
        /** @var \DOMNode $item */
        foreach ($this->crawler->filter($this->rootSelector . ' ' . $this->itemSelector) as $item) {
            yield $item;
        }
    }

    public function assertCount(int $count): void
    {
        Assert::assertCount(
            $count,
            iterator_to_array($this->iterator()),
            "Expected $count {$this->itemSelector} item(s) within {$this->rootSelector}."
        );
    }

    public function assertAny(callable $matcher): void
    {
        /** @var mixed $item */
        foreach ($this->iterator() as $item) {
            if ($matcher($item)) {
                Assert::assertTrue(true);
                return;
            }
        }

        throw new \LogicException("No items in list matched");
    }

    public function findFirstMatching(callable $matcher): mixed
    {
        /** @var mixed $item */
        foreach ($this->iterator() as $item) {
            if ($matcher($item)) {
                return $item;
            }
        }

        throw new \LogicException("No items in list matched");
    }

    public function getIterator(): \Generator
    {
        return $this->iterator();
    }

    public function findNth(int $itemIndex): ListElementItem
    {
        return new ListElementItem($this->crawler->filter("{$this->rootSelector} {$this->itemSelector}:nth-child($itemIndex)"));
    }
}
