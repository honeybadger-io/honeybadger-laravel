<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Queue\Events\JobProcessed as LaravelJobProcessed;

class JobProcessed extends ApplicationEvent
{
    public string $handles = LaravelJobProcessed::class;

    /**
     * @param LaravelJobProcessed $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $jobClass = get_class($event->job);

        $metadata = [
            'connectionName' => $event->connectionName,
            'job' => get_class($event->job),
            'duration' => Honeybadger::time($jobClass)
        ];

        return new EventPayload(
            'job',
            'job.processed',
            'Job processed',
            $metadata,
        );
    }

}
