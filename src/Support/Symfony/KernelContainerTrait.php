<?php

namespace Mashbo\CoreTesting\Support\Symfony;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @mixin KernelTestCase
 */
trait KernelContainerTrait
{
    abstract protected static function bootKernel(array $options = []);

    protected static function getKernel(): KernelInterface
    {
        if (static::$kernel === null) {
            static::bootKernel();
        }

        return static::$kernel;
    }

    protected function tearDown(): void
    {
        static::$kernel->shutdown();
        static::$kernel = null;
        static::$booted = false;
    }
}
