<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;

class HoneybadgerLaravelInstallCommand extends HoneybadgerInstallCommand
{
    /**
     * @inheritDoc
     */
    public function publishConfig()
    {
        return $this->callSilent('vendor:publish', [
                '--provider' => HoneybadgerServiceProvider::class,
            ]) === 0;
    }
}
