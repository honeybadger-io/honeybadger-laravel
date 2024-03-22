<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Contracts\Reporter;
use Honeybadger\LogHandler;
use Illuminate\Support\Facades\App;
use Monolog\Logger;

class HoneybadgerLogDriver
{
    public function __invoke(array $config): Logger
    {
        return tap(new Logger($config['name'] ?? 'honeybadger'), function ($logger) {
            $logHandler = App::makeWith(LogHandler::class, [
                App::make(Reporter::class),
                $config['level'] ?? 'error',
            ]);
            $logger->pushHandler($logHandler);
        });
    }
}
