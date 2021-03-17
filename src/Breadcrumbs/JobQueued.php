<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Queue\Events\JobQueued as LaravelJobQueued;

/**
 * The JobQueued event was introduced in Laravel 8.24, so this won't work on lower versions.
 */
class JobQueued extends Breadcrumb
{
    public $handles = LaravelJobQueued::class;

    public function handleEvent(LaravelJobQueued $event)
    {
        $metadata = [
            'connectionName' => $event->connectionName,
            'queue' => $event->job->queue,
            'job' => get_class($event->job),
        ];

        Honeybadger::addBreadcrumb('Job queued', $metadata, 'job');
    }
}
