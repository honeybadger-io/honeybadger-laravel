<?php

namespace Honeybadger\Tests;

use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerLumenInstallCommand;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger as HoneybadgerFacade;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerLaravelInstallCommand;

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

    /** @test */
    public function uses_the_lumen_install_command()
    {
        $this->app->singleton('honeybadger.isLumen', function () {
            return true;
        });

        $this->app->getProvider(HoneybadgerServiceProvider::class)->boot();

        $this->assertEquals(
            HoneybadgerLumenInstallCommand::class,
            get_class($this->app->make('command.honeybadger:install'))
        );
    }

    /** @test */
    public function uses_the_laravel_install_command()
    {
        $this->app->singleton('honeybadger.isLumen', function () {
            return false;
        });

        $this->app->getProvider(HoneybadgerServiceProvider::class)->boot();

        $this->assertEquals(
            HoneybadgerLaravelInstallCommand::class,
            get_class($this->app->make('command.honeybadger:install'))
        );
    }
}
