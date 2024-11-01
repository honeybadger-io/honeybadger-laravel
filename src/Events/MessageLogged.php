<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Honeybadger\HoneybadgerLaravel\HoneybadgerLogEventDriver;
use Illuminate\Log\Events\MessageLogged as LaravelMessageLogged;

/**
 * Note: This event should not be used for the Events API.
 * To send logs to Honeybadger Events API, use the {@link HoneybadgerLogEventDriver} as a log channel instead.
 * The log channel driver comes with built-in support for log levels and infinite loop protection (it uses Monolog).
 */
class MessageLogged extends ApplicationEvent
{
    public string $handles = LaravelMessageLogged::class;

    /**
     * @param LaravelMessageLogged $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = $event->context;
        $metadata['level'] = $event->level;

        $exception = $event->context['exception'] ?? null;
        if ($exception instanceof \Exception) {
            $metadata['exception'] = get_class($exception);
            $metadata['code'] = $exception->getCode();
            $metadata['file'] = $exception->getFile();
            $metadata['line'] = $exception->getLine();
        }

        return new EventPayload(
            'log',
            'log',
            $event->message,
            $metadata,
        );
    }
}
