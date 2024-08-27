<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Queue\Events\JobQueued as LaravelJobQueued;

/**
 * The JobQueued event was introduced in Laravel 8.24, so this won't work on lower versions.
 */
class JobQueued extends ApplicationEvent
{
    public string $handles = LaravelJobQueued::class;

    /**
     * @param LaravelJobQueued $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = [
            'connectionName' => $event->connectionName,
            'queue' => $event->job->queue,
            'job' => get_class($event->job),
        ];

        return new EventPayload(
            'job',
            'job.queued',
            'Job queued',
            $metadata,
        );
    }
}
