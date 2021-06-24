<?php

namespace Honeybadger\Tests;

use Honeybadger\Contracts\Reporter;
use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\Middleware\HoneybadgerContext;
use Honeybadger\HoneybadgerLaravel\Middleware\UserContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class UserContextMiddlewareTest extends TestCase
{
    /** @test */
    public function it_adds_the_user_context()
    {
        $honeybadger = $this->createMock(Reporter::class);

        $honeybadger->expects($this->once())
            ->method('context')
            ->with('user_id', '1234');

        $this->app[Reporter::class] = $honeybadger;

        $request = new Request;
        $request->setUserResolver(function () {
            return new class {
                public function getAuthIdentifier()
                {
                    return '1234';
                }
            };
        });

        $middleware = new UserContext($honeybadger);
        $middleware->handle($request, function () {
            //
        });
    }

    /** @test */
    public function it_sets_action_and_context()
    {
        $honeybadger = $this->createMock(Honeybadger::class);

        $honeybadger->expects($this->once())
            ->method('setComponent')
            ->with('Honeybadger\Tests\Fixtures\TestController');

        $honeybadger->expects($this->once())
            ->method('setAction')
            ->with('index');

        $this->app[Reporter::class] = $honeybadger;

        Route::middleware(HoneybadgerContext::class)
            ->namespace('Honeybadger\Tests\Fixtures')
            ->group(function () {
                Route::get('test', 'TestController@index');
            });

        $this->get('test');
    }

    /** @test */
    public function it_does_not_set_action_for_a_closure()
    {
        $honeybadger = $this->createMock(Honeybadger::class);

        $honeybadger->expects($this->once())
            ->method('setComponent')
            ->with('Closure');

        $honeybadger->expects($this->never())
            ->method('setAction');

        $this->app[Reporter::class] = $honeybadger;

        Route::get('test', function () {
            return response(null, 200);
        })->middleware(HoneybadgerContext::class);

        $this->get('test');
    }

    /** @test */
    public function it_does_not_set_action_for_invokable_controllers()
    {
        $honeybadger = $this->createMock(Honeybadger::class);

        $honeybadger->expects($this->once())
            ->method('setComponent')
            ->with('Honeybadger\Tests\Fixtures\TestInvokableController');

        $honeybadger->expects($this->never())
            ->method('setAction');

        $this->app[Reporter::class] = $honeybadger;

        Route::middleware(HoneybadgerContext::class)
            ->namespace('Honeybadger\Tests\Fixtures')
            ->group(function () {
                Route::get('test', 'TestInvokableController');
            });

        $this->get('test');
    }
}
