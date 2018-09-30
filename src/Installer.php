<?php

namespace Honeybadger\HoneybadgerLaravel;

use InvalidArgumentException;
use sixlive\DotenvEditor\DotenvEditor;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer as InstallerContract;

class Installer implements InstallerContract
{
    /**
     * Write the configurations to dotenv files.
     *
     * @param  array  $config
     * @param  string  $file
     * @return bool
     */
    public function writeConfig(array $config, string $filePath) : bool
    {
        try {
            $env = new DotenvEditor;
            $env->load($filePath);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        collect($config)->each(function ($value, $key) use ($env) {
            $env->set($key, $value);
        });

        return $env->save();
    }
}
