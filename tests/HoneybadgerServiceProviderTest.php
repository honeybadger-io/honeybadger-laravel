<?php

namespace Honeybadger\Tests;

use Honeybadger\LogHandler;
use Honeybadger\Honeybadger;
use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger as HoneybadgerFacade;

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
}
