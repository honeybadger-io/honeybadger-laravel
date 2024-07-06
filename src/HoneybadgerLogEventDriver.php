<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Contracts\Reporter;
use Honeybadger\LogEventHandler;
use Illuminate\Support\Facades\App;
use Monolog\Logger;

class HoneybadgerLogEventDriver
{
    public function __invoke(array $config): Logger
    {
        return tap(new Logger($config['name'] ?? 'honeybadger'), function ($logger) {
            $logHandler = App::makeWith(LogEventHandler::class, [
                App::make(Reporter::class),
                $config['level'] ?? 'info',
            ]);
            $logger->pushHandler($logHandler);
        });
    }
}
