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
            // The LogEventHandler is not needed for sending logs as events to Honeybadger.
            // This file will be removed in later versions.
            // This is handled by the MessageLogged event by the automatic instrumentation.
            // You can disable or enable which events to be sent to Honeybadger in the config (honeybadger->events->automatic).
            // $logHandler = App::makeWith(LogEventHandler::class, [
            //     App::make(Reporter::class),
            //     $config['level'] ?? 'info',
            // ]);
            // $logger->pushHandler($logHandler);
        });
    }
}
