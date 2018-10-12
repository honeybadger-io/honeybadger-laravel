<?php

namespace Honeybadger\HoneybadgerLaravel;

use Monolog\Logger;
use Illuminate\Support\Facades\App;
use Honeybadger\Contracts\Reporter;

class HoneybadgerLogDriver
{
    public function __invoke(array $config) : Logger
    {
        return tap(new Logger($config['name'] ?? 'honeybadger'), function ($logger) {
            $logger->pushHandler(
                new LogHandler(App::make(Reporter::class))
            );
        });
    }
}
