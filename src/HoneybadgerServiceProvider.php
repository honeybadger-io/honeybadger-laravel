<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Honeybadger;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Event;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerTestCommand;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerCheckinCommand;

class HoneybadgerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bindCommands();
            $this->registerCommands();

            $this->publishes([
                __DIR__.'/../config/honeybadger.php' => base_path('config/honeybadger.php'),
            ], 'config');
        }

        $this->registerMacros();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/honeybadger.php', 'honeybadger');

        $this->app->singleton(Honeybadger::class, function ($app) {
            return (new HoneybadgerLaravel)->make($app['config']['honeybadger']);
        });

        $this->app->alias(Honeybadger::class, 'honeybadger');
    }

    /**
     * @return void
     */
    private function registerCommands()
    {
        $this->commands([
            'command.honeybadger:test',
            'command.honeybadger:checkin',
        ]);
    }

    /**
     * @return void
     */
    private function bindCommands()
    {
        $this->app->bind(
            'command.honeybadger:test',
            HoneybadgerTestCommand::class
        );

        $this->app->bind(
            'command.honeybadger:checkin',
            HoneybadgerCheckinCommand::class
        );
    }

    /**
     * @return void
     */
    private function registerMacros()
    {
        Event::macro('thenPingHoneybadger', function ($id) {
            return $this->then(function () use ($id) {
                app(Honeybadger::class)->checkin($id);
            });
        });
    }
}
