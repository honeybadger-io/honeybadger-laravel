<?php

namespace Honeybadger\Tests;

use Mockery;
use Honeybadger\Honeybadger;
use Illuminate\Http\Request;
use Honeybadger\HoneybadgerLaravel\Middleware\UserContext;

class UserContextMiddlewareTest extends TestCase
{
    /** @test */
    public function it_adds_the_user_context()
    {
        $this->markTestSkipped('refactor');
        $honeybadgerMock = Mockery::mock(Honeybadger::class)->makePartial();
        $honeybadgerMock->shouldReceive('context')->once();

        $requestMock = Mockery::mock(Request::class)->makePartial();
        $requestMock->shouldReceive('user->getAuthIdentifier')
            ->once()
            ->andReturn(1);

        $middleware = new UserContext($honeybadgerMock);
        $middleware->handle($requestMock, function () {
        });
    }
}
