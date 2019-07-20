<?php

namespace Honeybadger\HoneybadgerLaravel\Middleware;

use Closure;
use Honeybadger\Contracts\Reporter;
use Illuminate\Support\Facades\Route;

class HoneybadgerContext
{
    /**
     * @var \Honeybadger\Honeybadger;
     */
    protected $honeybadger;

    /**
     * @param  \Honeybadger\Contracts\Reporter  $honeybadger
     */
    public function __construct(Reporter $honeybadger)
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
        if (app()->bound('honeybadger')) {
            $this->setUserContext($request);
            $this->setRouteActionContext();
        }

        return $next($request);
    }

    private function setRouteActionContext()
    {
        if (Route::getCurrentRoute()) {
            $routeAction = explode('@', Route::getCurrentRoute()->getActionName());

            $this->honeybadger->context(
                'component',
                $routeAction[0] ?? null
            );

            $this->honeybadger->context(
                'action',
                $routeAction[1] ?? null
            );
        }
    }

    private function setUserContext($request)
    {
        if ($request->user()) {
            $this->honeybadger->context(
                'user_id',
                $request->user()->getAuthIdentifier()
            );
        }
    }
}
