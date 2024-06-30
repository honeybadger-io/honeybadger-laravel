<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Routing\Events\RouteMatched as LaravelRouteMatched;

class RouteMatched extends ApplicationEvent
{
    public string $handles = LaravelRouteMatched::class;

    /**
     * @param LaravelRouteMatched $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $route = $event->route;
        $metadata = [
            'uri' => $route->uri,
            'methods' => implode(',', $route->methods),
            'handler' => is_object($route->action['uses']) ? get_class($route->action['uses']) : $route->action['uses'],
            'name' => $route->action['as'] ?? null,
        ];

        return new EventPayload(
            'request',
            'Route matched',
            $metadata,
        );
    }
}
