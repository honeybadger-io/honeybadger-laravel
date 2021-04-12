<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Contracts\Reporter;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;

class ContextManager
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

    public function setRouteAction(Request $request)
    {
        if (app('honeybadger.isLumen')) {
            $this->setLumenRouteActionContext($request);
        } else {
            $this->setLaravelRouteActionContext();
        }
    }

    private function setLumenRouteActionContext(Request $request)
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

    private function setLaravelRouteActionContext()
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

    public function setUserContext($request)
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
