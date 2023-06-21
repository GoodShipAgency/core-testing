<?php

namespace Mashbo\CoreTesting\Support\Symfony;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\HttpKernel\KernelInterface;

trait ConsoleTestTrait
{
    abstract protected static function getKernel(): KernelInterface;

    private function createApplication(): Application
    {
        static::getKernel()->boot();
        $application = new Application(static::getKernel());
        $application->setAutoExit(false);
        return $application;
    }

    protected function createApplicationTester(): ApplicationTester
    {
        return new ApplicationTester($this->createApplication());
    }

    protected function runCommand(string $command, array $arguments = []): void
    {
        if (getenv('TEST_TOKEN') !== false) {
            $command = 'TEST_TOKEN=' . getenv('TEST_TOKEN') . ' ' . $command;
        }
        $tester = $this->createApplicationTester();
        $input = array_merge(['command' => $command], $arguments);
        $exit = $tester->run($input, ['capture_stderr_separately' => true, 'verbosity' => Output::VERBOSITY_VERBOSE]);
        if ($exit === 0) {
            return;
        }

        echo $tester->getErrorOutput(true);
        throw new \LogicException("Command $command returned exit code $exit");
    }
}
