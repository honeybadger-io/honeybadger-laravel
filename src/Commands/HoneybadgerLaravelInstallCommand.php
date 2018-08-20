<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;

class HoneybadgerLaravelInstallCommand extends HoneybadgerInstallCommand
{
    /**
     * {@inheritdoc}
     */
    public function publishConfig()
    {
        return $this->callSilent('vendor:publish', [
                '--provider' => HoneybadgerServiceProvider::class,
            ]) === 0;
    }
}
