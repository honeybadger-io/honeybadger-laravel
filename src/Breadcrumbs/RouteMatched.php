<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Routing\Events\RouteMatched as LaravelRouteMatched;

class RouteMatched extends Breadcrumb
{
    public $handles = LaravelRouteMatched::class;

    public function handleEvent(LaravelRouteMatched $event)
    {
        $route = $event->route;
        $metadata = [
            'uri' => $route->uri,
            'methods' => implode(',', $route->methods),
            'handler' => is_object($route->action['uses']) ? get_class($route->action['uses']) : $route->action['uses'],
            'name' => $route->action['as'] ?? null,
        ];

        Honeybadger::addBreadcrumb('Route matched', $metadata, 'request');
    }
}
