<?php

namespace Honeybadger\HoneybadgerLaravel\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignRequestId
{
    /**
     * Add request id to the log context.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->headers->has('Request-Id')) {
            $requestId = $request->headers->get('Request-Id');
        }
        else {
            $requestId = (string) Str::uuid();

            $request->headers->set('Request-Id', $requestId);
        }

        Log::shareContext([
            'requestId' => $requestId
        ]);

        return $next($request);
    }
}
