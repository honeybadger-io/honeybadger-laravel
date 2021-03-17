<?php

namespace Honeybadger\Tests;

use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\CacheHit;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\CacheMiss;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\DatabaseQueryExecuted;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\DatabaseTransactionCommitted;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\DatabaseTransactionRolledBack;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\DatabaseTransactionStarted;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\JobQueued;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\MailSending;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\MailSent;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\MessageLogged;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\NotificationSending;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\NotificationSent;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\RouteMatched;
use Honeybadger\HoneybadgerLaravel\Breadcrumbs\ViewRendered;
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

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->exactly(2))
            ->method('addBreadcrumb')
            ->withConsecutive(
                [
                    'Route matched',
                    [
                        'uri' => 'test',
                        'methods' => 'GET,HEAD',
                        'handler' => 'Honeybadger\Tests\Fixtures\TestController@index',
                        'name' => 'testing',
                    ],
                    'request',
                ],
                [
                    'Route matched',
                    [
                        'uri' => 'testClosure',
                        'methods' => 'POST',
                        'handler' => 'Closure',
                        'name' => null,
                    ],
                    'request',
                ]);
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
        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->exactly(4))
            ->method('addBreadcrumb')
            ->withConsecutive(
                [
                    'Database transaction started',
                    ['connectionName' => 'test'],
                    'query',
                ],
                [
                    'Database query executed',
                    $this->callback(function ($metadata) {
                        return $metadata['sql'] === 'select * from "users"'
                            && $metadata['connectionName'] === 'test'
                            && preg_match('/\d\.\d\dms/', $metadata['duration']);
                    }),
                    'query',
                ],
                [
                    'Database transaction rolled back',
                    ['connectionName' => 'test'],
                    'query',
                ],
                [
                    'Database transaction committed',
                    ['connectionName' => 'test'],
                    'query',
                ]
            );
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

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->exactly(2))
            ->method('addBreadcrumb')
            ->withConsecutive(
                [
                    'Sending notification',
                    [
                        'notification' => TestNotification::class,
                        'channel' => 'mail',
                        'queue' => null,
                        'notifiable' => TestUser::class,
                    ],
                    'notification',
                ],
                [
                    'Notification sent',
                    [
                        'notification' => TestNotification::class,
                        'channel' => 'mail',
                        'queue' => null,
                        'notifiable' => TestUser::class,
                    ],
                    'notification',
                ]
            );
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

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->exactly(2))
            ->method('addBreadcrumb')
            ->withConsecutive(
                [
                    'Sending mail',
                    [
                        'queue' => null,
                        'replyTo' => null,
                        'to' => 'chunkylover53@aol.com',
                        'cc' => null,
                        'bcc' => null,
                        'subject' => 'HAhaHA',
                    ],
                    'mail',
                ],
                [
                    'Mail sent',
                    [
                        'queue' => null,
                        'replyTo' => null,
                        'to' => 'chunkylover53@aol.com',
                        'cc' => null,
                        'bcc' => null,
                        'subject' => 'HAhaHA',
                    ],
                    'mail',
                ]
            );
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

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->exactly(2))
            ->method('addBreadcrumb')
            ->withConsecutive(
                [
                    'Job queued',
                    [
                        'connectionName' => 'database',
                        'queue' => null,
                        'job' => 'Illuminate\Queue\CallQueuedClosure',
                    ],
                    'job',
                ],
                [
                    'Job queued',
                    [
                        'connectionName' => 'database',
                        'queue' => null,
                        'job' => TestJob::class,
                    ],
                    'job',
                ]
            );
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

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->exactly(2))
            ->method('addBreadcrumb')
            ->withConsecutive(
                [
                    'Cache miss',
                    [
                        'key' => 'user:profile',
                    ],
                    'query',
                ],
                [
                    'Cache hit',
                    [
                        'key' => 'user:profile',
                    ],
                    'query',
                ]
            );
        $this->app->instance(Reporter::class, $honeybadger);

        Cache::get('user:profile');
        Cache::put('user:profile', 5);
        Cache::get('user:profile');
    }
}
