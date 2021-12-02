<?php

declare(strict_types = 1);

namespace hanneskod\classtools\Tests;

class MockSplFileInfo extends \hanneskod\classtools\Iterator\SplFileInfo
{
    private string $contents;
    private string $path;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
        $tempnam = tempnam(sys_get_temp_dir(), 'CLASSTOOLS_');
        unlink($tempnam);
        $this->path = $tempnam . '.php';
        $handle = fopen($this->path, "w");
        fwrite($handle, $contents);
        fclose($handle);
    }

    public function __destruct()
    {
        unlink($this->path);
    }

    public function getPathname()
    {
        return $this->path;
    }

    public function getRealPath(): string
    {
        return $this->path;
    }

    public function getContents(): string
    {
        return $this->contents;
    }
}
