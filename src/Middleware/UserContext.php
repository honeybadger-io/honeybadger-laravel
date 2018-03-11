<?php

namespace Honeybadger\HoneybadgerLaravel\Middleware;

use Closure;
use Honeybadger\Honeybadger;

class UserContext
{
    /**
     * @var \Honeybadger\Honeybadger;
     */
    protected $honeybadger;

    /**
     * @param  \Honeybadger\Honeybadger  $honeybadger
     */
    public function __construct(Honeybadger $honeybadger)
    {
        $this->honeybadger = $honeybadger;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app()->bound('honeybadger') && $request->user()) {
            $this->honeybadger->context(
                'user_id',
                 $request->user()->getAuthIdentifier()
            );
        }

        return $next($request);
    }
}
