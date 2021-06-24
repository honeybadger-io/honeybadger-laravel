<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\LogHandler;
use Illuminate\Support\Facades\App;
use Monolog\Logger;

class HoneybadgerLogDriver
{
    public function __invoke(array $config): Logger
    {
        return tap(new Logger($config['name'] ?? 'honeybadger'), function ($logger) {
            $logger->pushHandler(App::make(LogHandler::class));
        });
    }
}
