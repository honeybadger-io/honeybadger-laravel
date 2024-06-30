<?php

namespace Honeybadger\HoneybadgerLaravel;

use GuzzleHttp\Client;
use Honeybadger\CheckInsManager;
use Honeybadger\Contracts\SyncCheckIns;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerCheckInsSyncCommand;
use Honeybadger\LogEventHandler;
use Honeybadger\LogHandler;
use Honeybadger\Honeybadger;
use Honeybadger\Contracts\Reporter;
use Honeybadger\Exceptions\ServiceException;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerCheckInCommand;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerDeployCommand;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerTestCommand;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer as InstallerContract;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class HoneybadgerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bindCommands();
            $this->registerCommands();
            $this->app->bind(InstallerContract::class, Installer::class);

            $this->registerPublishableAssets();
        }

        $this->registerEventHooks();
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'honeybadger');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'honeybadger');
        $this->registerBladeDirectives();
        $this->setUpAutomaticEvents();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/honeybadger.php', 'honeybadger');

        $this->registerReporters();
        $this->registerCheckInsSync();

        $this->app->bind(LogHandler::class, function ($app) {
            return new LogHandler($app[Reporter::class]);
        });

        $this->app->bind(LogEventHandler::class, function ($app) {
            return new LogEventHandler($app[Reporter::class]);
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
            'command.honeybadger:checkins:sync',
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
            HoneybadgerCheckInCommand::class
        );

        $this->app->bind(
            'command.honeybadger:checkins:sync',
            HoneybadgerCheckInsSyncCommand::class
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
    private function registerEventHooks()
    {
        /** @param string|array|null $environments */
        Event::macro('thenPingHoneybadger', function (string $checkinIdOrName, $environments = null) {
            return $this->then(function () use ($checkinIdOrName, $environments) {
                if ($environments === null || app()->environment($environments)) {
                    app(Reporter::class)->checkin($checkinIdOrName);
                }
            });
        });

        /** @param string|array|null $environments */
        Event::macro('pingHoneybadgerOnSuccess', function (string $checkinIdOrName, $environments = null) {
            return $this->onSuccess(function () use ($checkinIdOrName, $environments) {
                if ($environments === null || app()->environment($environments)) {
                    app(Reporter::class)->checkin($checkinIdOrName);
                }
            });
        });
    }

    private function registerBladeDirectives()
    {
        // Views are not enabled on Lumen by default
        if (app()->bound('blade.compiler')) {
            Blade::directive('honeybadgerError', function ($options) {
                if ($options === '') {
                    $options = '[]';
                }

                $defaults = "['class' => 'text-gray-500 text-sm', 'text' => 'Error ID:']";

                return "<?php echo \$__env->make('honeybadger::informer', $options, $defaults)->render(); ?>";
            });

            Blade::directive('honeybadgerFeedback', function () {
                $action = rtrim(Honeybadger::API_URL, '/').'/v1/feedback';

                return "<?php echo \$__env->make('honeybadger::feedback', ['action' => '$action'])->render(); ?>";
            });
        }
    }

    protected function registerPublishableAssets(): void
    {
        $this->publishes([
            __DIR__.'/../config/honeybadger.php' => base_path('config/honeybadger.php'),
        ], 'honeybadger-config');
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/honeybadger'),
        ], 'honeybadger-views');
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/honeybadger'),
        ], 'honeybadger-translations');
    }

    protected function setUpAutomaticEvents(): void
    {
        $breadcrumbsEnabled = config('honeybadger.breadcrumbs.enabled', true);
        $eventsEnabled = config('honeybadger.events.enabled', false);

        $mergedEvents = [];
        if ($breadcrumbsEnabled) {
            $breadcrumbEvents = (array) config('honeybadger.breadcrumbs.automatic', HoneybadgerLaravel::DEFAULT_EVENTS);

            // Replace deprecated event names with the new ones - need to make sure we don't register them twice
            $breadcrumbEvents = array_map(function ($event) {
                return str_replace('HoneybadgerLaravel\Breadcrumbs', 'HoneybadgerLaravel\Events', $event);
            }, $breadcrumbEvents);

            $mergedEvents = $breadcrumbEvents;
        }

        if ($eventsEnabled) {
            $events = (array) config('honeybadger.events.automatic', HoneybadgerLaravel::DEFAULT_EVENTS);
            $mergedEvents = array_merge($mergedEvents, $events);
        }

        $mergedEvents = array_unique($mergedEvents);
        if (empty($mergedEvents)) {
            return;
        }

        foreach ($mergedEvents as $event) {
            (new $event)->register();
        }
    }

    protected function registerCheckInsSync(): void
    {
        $this->app->singleton(SyncCheckIns::class, function ($app) {
            return new CheckInsManager($app['config']['honeybadger']);
        });
    }

    protected function registerReporters(): void
    {
        $this->app->singleton(Reporter::class, function ($app) {
            return HoneybadgerLaravel::make($app['config']['honeybadger']);
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

            return HoneybadgerLaravel::make($config);
        });
    }
}
