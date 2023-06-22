<?php

namespace Mashbo\CoreTesting\Support\Filesystem;

trait FilesystemTestTrait
{
    protected string $userdataPath = '/app/tests/userdata/test';

    protected function pathForUserdata(string $relativePath): string
    {
        return $this->getRootUserdataPath() . $relativePath;
    }

    private function getRootUserdataPath(): string
    {
        if (getenv('TEST_TOKEN') !== false) {
            return $this->userdataPath . getenv('TEST_TOKEN') . '/';
        }

        return $this->userdataPath . '/';
    }

    /**
     * @before
     */
    public function resetFilesBeforeTest(): void
    {
        shell_exec("rm -rf {$this->getRootUserdataPath()}* 2>/dev/null");
    }
}
