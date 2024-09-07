<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Queue\Events\JobProcessing as LaravelJobProcessing;

class JobProcessing extends ApplicationEvent
{
    public string $handles = LaravelJobProcessing::class;

    /**
     * @param LaravelJobProcessing $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $jobClass = get_class($event->job);

        Honeybadger::time($jobClass);

        $metadata = [
            'connectionName' => $event->connectionName,
            'queue' => $event->job->queue,
            'job' => $jobClass,
        ];

        return new EventPayload(
            'job',
            'job.processing',
            'Job processing',
            $metadata,
        );
    }
}
