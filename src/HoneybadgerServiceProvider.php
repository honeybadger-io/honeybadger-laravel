<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Honeybadger;
use Illuminate\Support\ServiceProvider;
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
            $this->bindTestCommand();
            $this->bindCheckinCommand();

            $this->commands([
                'command.honeybadger:test',
                'command.honeybadger:checkin',
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/honeybadger.php' => base_path('config/honeybadger.php'),
        ], 'config');
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
    private function bindTestCommand()
    {
        $this->app->bind(
            'command.honeybadger:test',
            HoneybadgerTestCommand::class
        );
    }

    /**
     * @return void
     */
    private function bindCheckinCommand()
    {
        $this->app->bind(
            'command.honeybadger:checkin',
            HoneybadgerCheckinCommand::class
        );
    }
}
