<?php

namespace Honeybadger\HoneybadgerLaravel\Middleware;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Symfony\Component\HttpFoundation\Response;

class FlushEvents
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle($request, $next): Response
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate($request, $response): void
    {
        Honeybadger::flushEvents();
    }
}
