<?php

namespace Honeybadger\HoneybadgerLaravel;

use GuzzleHttp\Client;
use Honeybadger\Contracts\Reporter;
use Honeybadger\Exceptions\ServiceException;
use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerCheckinCommand;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerDeployCommand;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerTestCommand;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer as InstallerContract;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\ServiceProvider;

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
            $this->app->bind(InstallerContract::class, Installer::class);

            $this->publishes([
                __DIR__ . '/../config/honeybadger.php' => base_path('config/honeybadger.php'),
            ], 'config');
        }

        $this->registerMacros();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/honeybadger.php', 'honeybadger');

        $this->app->singleton(Reporter::class, function ($app) {
            return (new HoneybadgerLaravel)->make($app['config']['honeybadger']);
        });

        $this->app->alias(Reporter::class, Honeybadger::class);
        $this->app->alias(Reporter::class, 'honeybadger');

        // In some cases (like the test command), we definitely want to throw any errors
        // Laravel's contextual binding doesn't support method injection,
        // so the handle() method will have to request this client specifically.
        $this->app->singleton('honeybadger.loud', function ($app) {
            $config = $app['config']['honeybadger'];
            $config['service_exception_handler'] = function (ServiceException $e) {
                throw $e;
            };
            return (new HoneybadgerLaravel)->make($config);
        });

        $this->app->singleton('honeybadger.isLumen', function () {
            return preg_match('/lumen/i', $this->app->version());
        });

        $this->app->when(HoneybadgerDeployCommand::class)
            ->needs(Client::class)
            ->give(function () {
                return new Client([
                    'http_errors' => false,
                ]);
            });
    }

    /**
     * @return void
     */
    private function registerCommands()
    {
        $this->commands([
            'command.honeybadger:test',
            'command.honeybadger:checkin',
            'command.honeybadger:install',
            'command.honeybadger:deploy',
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

        $this->app->bind(
            'command.honeybadger:install',
            HoneybadgerInstallCommand::class
        );

        $this->app->bind(
            'command.honeybadger:deploy',
            HoneybadgerDeployCommand::class
        );
    }

    /**
     * @return void
     */
    private function registerMacros()
    {
        Event::macro('thenPingHoneybadger', function ($id) {
            return $this->then(function () use ($id) {
                app(Reporter::class)->checkin($id);
            });
        });

        Event::macro('pingHoneybadgerOnSuccess', function ($id) {
            return $this->onSuccess(function () use ($id) {
                app(Reporter::class)->checkin($id);
            });
        });
    }
}
