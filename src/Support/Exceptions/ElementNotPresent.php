<?php

namespace Mashbo\CoreTesting\Support\Exceptions;

class ElementNotPresent extends \LogicException
{
    public function __construct(string $selector)
    {
        parent::__construct("Element with selector $selector was not found on page");
    }
}
