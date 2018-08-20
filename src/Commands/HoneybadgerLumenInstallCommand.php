<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Honeybadger\Honeybadger;

class HoneybadgerLumenInstallCommand extends HoneybadgerInstallCommand
{
    /**
     * {@inheritdoc}
     */
    public function publishConfig()
    {
        if (! is_dir(base_path('config'))) {
            mkdir(base_path('config'));
        }

        return copy(
            __DIR__.'/../../config/honeybadger.php',
            base_path('config/honeybadger.php')
        );
    }
}
