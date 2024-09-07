<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Events\Dispatcher;
use Illuminate\Queue\Events\JobProcessed as LaravelJobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class JobProcessed extends ApplicationEvent
{
    public string $handles = LaravelJobProcessed::class;

    private float $startTime;

    /**
     * @param LaravelJobProcessed $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $job = $event->job;
        $metadata = [
            'connectionName' => $event->connectionName,

            // we have to call 'resolveName' because sometimes the actual job is wrapped (i.e. DatabaseJob -> Job)
            'job' => $job->resolveName(),

            'id' => $job->getJobId(),

            // number of attempts made to process the job
            'attempts' => $job->attempts(),

            // if the job has been marked as failed
            'hasFailed' => $job->hasFailed(),

            // if the job has been released back into the queue
            'isReleased' => $job->isReleased(),

            // if the job has been deleted (i.e. it's not in the queue anymore)
            'isDeleted' => $job->isDeleted(),

            // number of times the job can be retried
            'maxTries' => $job->maxTries(),

            // number of exceptions that can be thrown before the job is considered failed (regardless of the number of attempts)
            'maxExceptions' => $job->maxExceptions(),

            // number of seconds the job can run before it's considered timed out
            'timeout' => $job->timeout(),

            // timestamp indicating when the job should timeout
            'retryUntil' => $job->retryUntil(),

            // duration in seconds of the job processing
            // calculated by measuring the time difference since the JobProcessing event was raised
            'duration' => $this->getDuration(),
        ];

        return new EventPayload(
            'job',
            'job.processed',
            'Job processed',
            $metadata,
        );
    }

    /**
     * Register the listeners for the subscriber.
     * We are overriding the register() method from the parent class,
     * so we can listen to the JobProcessing event as well.
     */
    public function register(): void
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = app('events');

        $dispatcher->listen(JobProcessing::class, function ($event) {
            $this->startTime = microtime(true);
        });

        $dispatcher->listen($this->handles, [$this, 'handle']);

    }

    /**
     * Calculate the duration of the job processing,
     * by measuring the time difference since the JobProcessing event was raised.
     */
    private function getDuration(): ?float {
        if (!isset($this->startTime)) {
            return null;
        }

        $endTime = microtime(true);
        return floor(($endTime - $this->startTime) * 1000);
    }

}
