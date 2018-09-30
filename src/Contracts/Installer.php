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
    public function writeConfig(array $cofng, string $filePath) : bool;
}
