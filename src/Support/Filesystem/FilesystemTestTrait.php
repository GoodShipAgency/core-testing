<?php

namespace Mashbo\CoreTesting\Support\Filesystem;

trait FilesystemTestTrait
{
    protected string $userdataPath = '/app/tests/userdata/';

    protected function pathForUserdata(string $relativePath): string
    {
        return $this->userdataPath . $relativePath;
    }

    /**
     * @before
     */
    public function resetFilesBeforeTest(): void
    {
        //shell_exec("rm -rf {$this->userdataPath}* 2>/dev/null");
    }
}
