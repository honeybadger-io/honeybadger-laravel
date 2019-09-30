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

            if (app('honeybadger.isLumen')) {
                $this->setLumenRouteActionContext($request);
            } else {
                $this->setRouteActionContext();
            }
        }

        return $next($request);
    }

    private function setLumenRouteActionContext($request)
    {
        $routeDetails = app()->router->getRoutes()[$request->method().$request->getPathInfo()]['action'];

        if (! isset($routeDetails['uses']) && ! empty($routeDetails[0])) {
            $this->honeybadger->setComponent(get_class($routeDetails[0]));

            return;
        }

        $routeAction = explode('@', $routeDetails['uses']);

        if (! empty($routeAction[0])) {
            $this->honeybadger->setComponent($routeAction[0] ?? '');
        }

        if (! empty($routeAction[1])) {
            $this->honeybadger->setAction($routeAction[1] ?? '');
        }
    }

    private function setRouteActionContext()
    {
        if (Route::getCurrentRoute()) {
            $routeAction = explode('@', Route::getCurrentRoute()->getActionName());

            if (! empty($routeAction[0])) {
                $this->honeybadger->setComponent($routeAction[0] ?? '');
            }

            if (! empty($routeAction[1])) {
                $this->honeybadger->setAction($routeAction[1] ?? '');
            }
        }
    }

    private function setUserContext($request)
    {
        try {
            if ($request->user()) {
                $this->honeybadger->context(
                    'user_id',
                    $request->user()->getAuthIdentifier()
                );
            }
        } catch (\InvalidArgumentException $e) {
            // swallow
        }
    }
}
