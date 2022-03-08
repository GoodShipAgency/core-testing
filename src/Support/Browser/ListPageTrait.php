<?php

declare(strict_types=1);

namespace Mashbo\CoreTesting\Support\Browser;

use Mashbo\CoreTesting\Support\ElementObjects\ListElement;
use Mashbo\CoreTesting\Support\ElementObjects\PaginationControlElement;
use Mashbo\CoreTesting\Support\PageObjects\PageObject;
use PHPUnit\Framework\Assert;

/** @mixin PageObject */
trait ListPageTrait
{
    protected function getListElementRootSelector(): string
    {
        return '.items';
    }

    protected function getListElementItemSelector(): string
    {
        return '.item';
    }

    private function getListElement(): ListElement
    {
        return new ListElement($this->browser->getCrawler(), $this->getListElementRootSelector(), $this->getListElementItemSelector());
    }

    public function assertCount(int $expectedCount): self
    {
        $this
            ->getListElement()
            ->assertCount($expectedCount);

        return $this;
    }

    public function assertPaginatedResultRange(int $firstNumber, int $lastNumber): self
    {
        $this->getPaginationControlElement()->assertRange($firstNumber, $lastNumber);

        return $this;
    }

    public function assertPreviousPageNotAvailable(): self
    {
        Assert::assertFalse($this->getPaginationControlElement()->isPreviousAvailable());

        return $this;
    }

    public function assertPreviousPageAvailable(): self
    {
        Assert::assertTrue($this->getPaginationControlElement()->isPreviousAvailable());

        return $this;
    }

    public function assertNextPageNotAvailable(): self
    {
        Assert::assertFalse($this->getPaginationControlElement()->isNextAvailable());

        return $this;
    }

    public function assertNextPageAvailable(): self
    {
        Assert::assertTrue($this->getPaginationControlElement()->isNextAvailable());

        return $this;
    }

    private function getPaginationControlElement(): PaginationControlElement
    {
        return new PaginationControlElement($this->browser->getCrawler(), '.pagination');
    }

    public function nextPage(): self
    {
        $this->browser->click($this->getPaginationControlElement()->getNextLink()->link());

        return new self($this->browser);
    }

    public function previousPage(): self
    {
        $this->browser->click($this->getPaginationControlElement()->getPreviousLink()->link());

        return new self($this->browser);
    }
}
