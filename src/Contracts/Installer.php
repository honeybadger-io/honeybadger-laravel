<?php

namespace Honeybadger\HoneybadgerLaravel\Contracts;

interface Installer
{
    /**
     * Write the configurations to dotenv files.
     *
     * @param  array  $config
     * @param  string  $file
     * @return bool
     */
    public function writeConfig(array $config, string $filePath) : bool;

    public function publishConfig() : bool;

    public function shouldPublishConfig() : bool;

    public function sendTestException() : array;
}
