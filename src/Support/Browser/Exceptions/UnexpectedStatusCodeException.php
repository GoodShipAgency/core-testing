<?php

namespace Mashbo\CoreTesting\Support\Browser\Exceptions;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UnexpectedStatusCodeException extends \LogicException
{
    private int $statusCode;

    public function __construct(Request $request, Response $response, string $expected)
    {
        $this->statusCode = $statusCode = $response->getStatusCode();
        $method = $request->getMethod();
        $path = $request->getPathInfo();
        parent::__construct("Got status code {$statusCode} on {$method} {$path}. Expected {$expected}");
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
