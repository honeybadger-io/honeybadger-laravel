<?php

namespace Honeybadger\Tests;

use Honeybadger\Contracts\Reporter;
use Honeybadger\Honeybadger;
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
}
