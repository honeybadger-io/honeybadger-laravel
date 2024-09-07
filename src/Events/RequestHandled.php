<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Foundation\Http\Events\RequestHandled as LaravelRequestHandled;

class RequestHandled extends ApplicationEvent
{
    public string $handles = LaravelRequestHandled::class;

    /**
     * @param LaravelRequestHandled $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $request = $event->request;
        $response = $event->response;

        $routeName = null;
        $method = null;
        $controller = null;
        $route = $request->route();
        if (isset($route)) {
            $routeName = $route->getName();
            $method = $route->getActionMethod();
            $controller = $route->getControllerClass();
        }

        $metadata = [
            'uri' => $request->url(),
            'method' => $request->getMethod(),
            'statusCode' => $response->getStatusCode(),
            'duration' => $this->getDurationInMs(LARAVEL_START),
            'controller' => $controller,
            'action' => $method,
            'routeName' => $routeName,
        ];

        return new EventPayload(
            'request',
            'request.handled',
            'Request handled',
            $metadata,
        );
    }
}
