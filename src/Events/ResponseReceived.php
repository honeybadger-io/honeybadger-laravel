<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Events\Dispatcher;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived as LaravelResponseReceived;

class ResponseReceived extends ApplicationEvent
{
    public string $handles = LaravelResponseReceived::class;

    private float $startTime;

    /**
     * @param LaravelResponseReceived $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = [
            'uri' => $event->request->url(),
            'statusCode' => $event->response->status(),
            'duration' => $this->getDurationInMs($this->startTime),
        ];

        return new EventPayload(
            'response',
            'response.received',
            'Outbound response received',
            $metadata,
        );
    }

    public function register(): void {
        /** @var Dispatcher $dispatcher */
        $dispatcher = app('events');

        $dispatcher->listen(RequestSending::class, function ($event) {
            $this->startTime = microtime(true);
        });

        $dispatcher->listen($this->handles, [$this, 'handle']);
    }
}
