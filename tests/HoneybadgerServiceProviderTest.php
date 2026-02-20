<?php

namespace Honeybadger\Tests;

use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;
use Honeybadger\HoneybadgerLaravel\Middleware\AssignRequestId;
use Honeybadger\HoneybadgerLaravel\Middleware\FlushEvents;
use Honeybadger\LogEventHandler;
use Honeybadger\LogHandler;
use Honeybadger\Honeybadger;
use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger as HoneybadgerFacade;
use Illuminate\Contracts\Http\Kernel;

class HoneybadgerServiceProviderTest extends TestCase
{
    public function test_facade_will_resolve_an_instance()
    {
        $this->assertEquals('honeybadger', HoneybadgerFacade::getFacadeAccessor());
    }

    public function test_aliases_are_set()
    {
        $this->assertInstanceOf(Honeybadger::class, $this->app[Reporter::class]);
        $this->assertInstanceOf(Honeybadger::class, $this->app[Honeybadger::class]);
        $this->assertInstanceOf(Honeybadger::class, $this->app['honeybadger']);
    }

    public function test_it_registers_the_log_handler()
    {
        $this->assertInstanceOf(LogHandler::class, $this->app[LogHandler::class]);
    }

    public function test_it_registers_the_log_event_handler()
    {
        $this->assertInstanceOf(LogEventHandler::class, $this->app[LogEventHandler::class]);
    }

    public function test_it_registers_middleware_by_default()
    {
        $this->partialMock(Kernel::class, function ($mock) {
            $mock->shouldReceive('prependMiddleware')
                ->with(AssignRequestId::class)
                ->once();

            $mock->shouldReceive('pushMiddleware')
                ->with(FlushEvents::class)
                ->once();
        });
        $provider = new HoneybadgerServiceProvider($this->app);
        $provider->boot();
    }

    public function test_it_registers_only_flush_events_middleware_when_disabled()
    {
        $this->app['config']->set('honeybadger.middleware', []);
        $this->app['config']->set('honeybadger.events.enabled', true);
        $this->partialMock(Kernel::class, function ($mock) {
            $mock->shouldReceive('prependMiddleware')
                ->with(AssignRequestId::class)
                ->never();

            // flush events middleware is always registered
            $mock->shouldReceive('pushMiddleware')
                ->with(FlushEvents::class)
                ->once();
        });
        $provider = new HoneybadgerServiceProvider($this->app);
        $provider->boot();
    }
}
