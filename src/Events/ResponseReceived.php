<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class ResponseReceived extends ApplicationEvent
{
    public function getEventPayload($event): EventPayload
    {
        $metadata = [
            'uri' => $event['request']->getUri(),
            'statusCode' => $event['response']->getStatusCode(),
            'duration' => $event['duration'],
        ];

        return new EventPayload(
            'response',
            'response.received',
            'Outbound response received',
            $metadata,
        );
    }

    public function register(): void {
        \Illuminate\Support\Facades\Http::globalMiddleware(function ($handler) {
            return function (Request $request, $options) use ($handler) {
                $startTime = microtime(true);

                return $handler($request, $options)
                    ->then(function (Response $response) use ($request, $startTime) {
                        $this->handle([
                            'request' => $request,
                            'response' => $response,
                            'duration' => $this->getDurationInMs($startTime),
                        ]);

                        return $response;
                    });
            };
        });
    }
}
