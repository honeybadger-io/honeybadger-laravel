<?php

namespace Honeybadger\Tests;

use Illuminate\Http\Request;
use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Middleware\UserContext;

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
}
