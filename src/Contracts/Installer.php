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

    /**
     * Publish the config file for Laravel.
     *
     * @return bool
     */
    public function publishLaravelConfig() : bool;

    /**
     * Publish the config file for Lumen.
     *
     * @return bool
     */
    public function publishLumenConfig() : bool;

    /**
     * Whether the config file needs to be published or not.
     *
     * @return bool
     */
    public function shouldPublishConfig() : bool;

    /**
     * Send a test exception to Honeybadger.
     *
     * @return array
     */
    public function sendTestException() : array;
}
