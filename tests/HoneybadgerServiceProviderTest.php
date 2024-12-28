<?php

namespace Honeybadger\Tests;

use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;
use Honeybadger\HoneybadgerLaravel\Middleware\AssignRequestId;
use Honeybadger\LogEventHandler;
use Honeybadger\LogHandler;
use Honeybadger\Honeybadger;
use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger as HoneybadgerFacade;
use Illuminate\Contracts\Http\Kernel;

class HoneybadgerServiceProviderTest extends TestCase
{
    /** @test */
    public function facade_will_resolve_an_instance()
    {
        $this->assertEquals('honeybadger', HoneybadgerFacade::getFacadeAccessor());
    }

    /** @test */
    public function aliases_are_set()
    {
        $this->assertInstanceOf(Honeybadger::class, $this->app[Reporter::class]);
        $this->assertInstanceOf(Honeybadger::class, $this->app[Honeybadger::class]);
        $this->assertInstanceOf(Honeybadger::class, $this->app['honeybadger']);
    }

    /** @test */
    public function it_registers_the_log_handler()
    {
        $this->assertInstanceOf(LogHandler::class, $this->app[LogHandler::class]);
    }

    /** @test */
    public function it_registers_the_log_event_handler()
    {
        $this->assertInstanceOf(LogEventHandler::class, $this->app[LogEventHandler::class]);
    }

    /** @test */
    public function it_registers_middleware_by_default()
    {
        $this->partialMock(Kernel::class, function ($mock) {
            $mock->shouldReceive('prependMiddleware')
                ->with(AssignRequestId::class)
                ->once();
        });
        $provider = new HoneybadgerServiceProvider($this->app);
        $provider->boot();
    }

    /** @test */
    public function it_does_not_register_middleware_when_disabled()
    {
        $this->app['config']->set('honeybadger.middleware', []);
        $this->partialMock(Kernel::class, function ($mock) {
            $mock->shouldReceive('prependMiddleware')
                ->with(AssignRequestId::class)
                ->never();
        });
        $provider = new HoneybadgerServiceProvider($this->app);
        $provider->boot();
    }
}
