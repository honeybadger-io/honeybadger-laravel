<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Foundation\Http\Events\RequestHandled as LaravalRequestHandled;

class RequestHandled extends ApplicationEvent
{
    public string $handles = LaravalRequestHandled::class;

    /**
     * @param LaravalRequestHandled $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $request = $event->request;
        $response = $event->response;

        $action = $request->route()->getActionName();
        list($controller, $method) = explode('@', $action);

        $metadata = [
            'uri' => $request->url(),
            'method' => $request->getMethod(),
            'status_code' => $response->getStatusCode(),
            'duration' => number_format((microtime(true) - LARAVEL_START) * 1000, 2, '.', '').'ms',
            'controller' => $controller,
            'action' => $method,
            'route_name' => $request->route()->getName(),
        ];

        return new EventPayload(
            'request',
            'request.handled',
            'Request handled',
            $metadata,
        );
    }
}
