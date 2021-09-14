<?php

namespace Mashbo\CoreTesting\Support\Symfony;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

trait KernelContainerTrait
{
    /**
     * @var KernelInterface
     */
    protected static $kernel;

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
