<?php

namespace Honeybadger\Tests\Commands;

use Honeybadger\HoneybadgerLaravel\Contracts\Installer as InstallerContract;

class InstallerFake implements InstallerContract
{
    public function __construct()
    {
    }

    public function writeConfig(array $config, string $filePath) : bool
    {
        return true;
    }

    public function sendTestException() : array
    {
        return [
            'id' => str_random(5),
        ];
    }
}
