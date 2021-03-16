<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Concerns\HandlesEvents;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Log\Events\MessageLogged as LaravelMessageLogged;

class MessageLogged
{
    use HandlesEvents;

    public $handles = LaravelMessageLogged::class;

    public function handleEvent(LaravelMessageLogged $event)
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

        Honeybadger::addBreadcrumb($event->message, $metadata, 'log');
    }
}
