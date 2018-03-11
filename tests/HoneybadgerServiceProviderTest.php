<?php

namespace Honeybadger\Tests;

use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger as HoneybadgerFacade;

class HoneybadgerServiceProviderTest extends TestCase
{
    /** @test */
    public function a_instance_will_be_resolved()
    {
        $this->app['config']->set('honeybadger.api_key', '1234');
        $honeybadger = $this->app->make(Honeybadger::class);

        $this->assertTrue($this->app->bound(Honeybadger::class));
        $this->assertInstanceOf(Honeybadger::class, $honeybadger);
    }

    /** @test */
    public function facade_will_resolve_an_instance()
    {
        $this->assertEquals(Honeybadger::class, $this->app->getAlias('honeybadger'));

        $this->assertEquals('honeybadger', HoneybadgerFacade::getFacadeAccessor());
    }
}
