<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Log\Events\MessageLogged as LaravelMessageLogged;

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
