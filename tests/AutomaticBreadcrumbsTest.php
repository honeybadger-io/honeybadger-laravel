<?php

namespace Honeybadger\Tests;

use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Events\CacheHit;
use Honeybadger\HoneybadgerLaravel\Events\CacheMiss;
use Honeybadger\HoneybadgerLaravel\Events\DatabaseQueryExecuted;
use Honeybadger\HoneybadgerLaravel\Events\DatabaseTransactionCommitted;
use Honeybadger\HoneybadgerLaravel\Events\DatabaseTransactionRolledBack;
use Honeybadger\HoneybadgerLaravel\Events\DatabaseTransactionStarted;
use Honeybadger\HoneybadgerLaravel\Events\JobQueued;
use Honeybadger\HoneybadgerLaravel\Events\MailSending;
use Honeybadger\HoneybadgerLaravel\Events\MailSent;
use Honeybadger\HoneybadgerLaravel\Events\MessageLogged;
use Honeybadger\HoneybadgerLaravel\Events\NotificationSending;
use Honeybadger\HoneybadgerLaravel\Events\NotificationSent;
use Honeybadger\HoneybadgerLaravel\Events\RouteMatched;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\RouteMatched as RouteMatchedDeprecated;
use Honeybadger\HoneybadgerLaravel\Events\ViewRendered;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Honeybadger\Tests\Fixtures\TestJob;
use Honeybadger\Tests\Fixtures\TestMailable;
use Honeybadger\Tests\Fixtures\TestNotification;
use Honeybadger\Tests\Fixtures\TestUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

class AutomaticBreadcrumbsTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /** @test */
    public function adds_breadcrumbs_for_routes()
    {
        Config::set('honeybadger.breadcrumbs.automatic', [RouteMatched::class]);
        Route::namespace('Honeybadger\Tests\Fixtures')
            ->group(function () {
                Route::get('test', 'TestController@index')->name('testing');
            });
        Route::post('testClosure', function () {
            return response()->json([]);
        });

        $matcher = $this->exactly(2);
        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($matcher)
            ->method('addBreadcrumb')
            ->willReturnCallback(function ($message, $metadata, $category) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals([
                        'uri' => 'test',
                        'methods' => 'GET,HEAD',
                        'handler' => 'Honeybadger\Tests\Fixtures\TestController@index',
                        'name' => 'testing',
                    ], $metadata),
                    2 => $this->assertEquals([
                        'uri' => 'testClosure',
                        'methods' => 'POST',
                        'handler' => 'Closure',
                        'name' => null,
                    ], $metadata)
                };
            });
        $this->app->instance(Reporter::class, $honeybadger);

        $this->get('test');
        $this->post('/testClosure');
    }

    /** @test */
    public function adds_breadcrumbs_for_logs()
    {
        Config::set('honeybadger.breadcrumbs.automatic', [MessageLogged::class]);

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->once())
            ->method('addBreadcrumb')
            ->with('Covfefe', ['level' => 'info'], 'log');

        $this->app->instance(Reporter::class, $honeybadger);

        Log::info('Covfefe');
    }

    /** @test */
    public function adds_breadcrumbs_for_views()
    {
        Config::set('honeybadger.breadcrumbs.automatic', [ViewRendered::class]);
        Config::set('view.paths', [realpath(__DIR__.'/Fixtures/views')]);
        Route::get('test', function () {
            return view('test');
        });

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->once())
            ->method('addBreadcrumb')
            ->with('View rendered', [
                'name' => 'test',
                'path' => realpath(__DIR__.'/Fixtures/views').'/test.blade.php',
            ], 'render');

        $this->app->instance(Reporter::class, $honeybadger);

        $this->get('test');
    }

    /** @test */
    public function adds_breadcrumbs_for_database_queries()
    {
        Config::set('honeybadger.breadcrumbs.automatic', [
            DatabaseTransactionStarted::class,
            DatabaseTransactionCommitted::class,
            DatabaseTransactionRolledBack::class,
            DatabaseQueryExecuted::class,
        ]);
        $this->loadLaravelMigrations();

        Honeybadger::clearResolvedInstances();
        $matcher = $this->exactly(4);
        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($matcher)
            ->method('addBreadcrumb')
            ->willReturnCallback(function ($message, $metadata, $category) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals('Database transaction started', $message),
                    2 => $this->assertEquals('Database query executed', $message),
                    3 => $this->assertEquals('Database transaction rolled back', $message),
                    4 => $this->assertEquals('Database transaction committed', $message)
                };
            });
        $this->app->instance(Reporter::class, $honeybadger);

        DB::beginTransaction();
        DB::table('users')->select('*')->get();
        DB::rollBack();
        DB::commit();
    }

    /** @test */
    public function adds_breadcrumbs_for_notifications()
    {
        Config::set('honeybadger.breadcrumbs.automatic', [NotificationSending::class, NotificationSent::class]);
        Config::set('mail.default', 'log');

        $matcher = $this->exactly(2);
        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($matcher)
            ->method('addBreadcrumb')
            ->willReturnCallback(function ($message, $metadata, $category) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals('Sending notification', $message),
                    2 => $this->assertEquals('Notification sent', $message),
                };
            });
        $this->app->instance(Reporter::class, $honeybadger);

        $user = new TestUser;
        Notification::send($user, new TestNotification);
    }

    /** @test */
    public function adds_breadcrumbs_for_mail()
    {
        Config::set('honeybadger.breadcrumbs.automatic', [MailSending::class, MailSent::class]);
        Config::set('view.paths', [realpath(__DIR__.'/Fixtures/views')]);
        Config::set('mail.default', 'log');

        $matcher = $this->exactly(2);
        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($matcher)
            ->method('addBreadcrumb')
            ->willReturnCallback(function ($message, $metadata, $category) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals('Sending mail', $message),
                    2 => $this->assertEquals('Mail sent', $message),
                };
            });
        $this->app->instance(Reporter::class, $honeybadger);

        Mail::to('chunkylover53@aol.com')->send(new TestMailable);
    }

    /** @test */
    public function adds_breadcrumbs_for_jobs()
    {
        if (version_compare($this->app->version(), '8.24.0', '<')) {
            $this->markTestSkipped('The JobQueued event was introduced in Laravel 8.24.0.');

            return;
        }

        Config::set('honeybadger.breadcrumbs.automatic', [JobQueued::class]);
        Config::set('queue.default', 'database');
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/migrations');

        $matcher = $this->exactly(2);
        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($matcher)
            ->method('addBreadcrumb')
            ->willReturnCallback(function ($message, $metadata, $category) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals([
                        'connectionName' => 'database',
                        'queue' => null,
                        'job' => 'Illuminate\Queue\CallQueuedClosure',
                    ], $metadata),
                    2 => $this->assertEquals([
                        'connectionName' => 'database',
                        'queue' => null,
                        'job' => TestJob::class,
                    ], $metadata)
                };
            });
        $this->app->instance(Reporter::class, $honeybadger);

        dispatch(function () {
            // nothing doin'
        });
        dispatch(new TestJob);
    }

    /** @test */
    public function adds_breadcrumbs_for_cache()
    {
        if (version_compare($this->app->version(), '8.24.0', '<')) {
            $this->markTestSkipped('The JobQueued event was introduced in Laravel 8.24.0.');

            return;
        }

        Config::set('honeybadger.breadcrumbs.automatic', [CacheHit::class, CacheMiss::class]);

        $matcher = $this->exactly(2);
        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($matcher)
            ->method('addBreadcrumb')
            ->willReturnCallback(function ($message, $metadata, $category) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals([
                        'key' => 'user:profile',
                    ], $metadata),
                    2 => $this->assertEquals([
                        'key' => 'user:profile',
                    ], $metadata)
                };
            });
        $this->app->instance(Reporter::class, $honeybadger);

        Cache::get('user:profile');
        Cache::put('user:profile', 5);
        Cache::get('user:profile');
    }
}
