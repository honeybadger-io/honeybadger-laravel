<?php

namespace Honeybadger\Tests;

use Honeybadger\Contracts\Reporter;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;

class HoneybadgerEventPingTest extends TestCase
{
    /** @test */
    public function scheduled_tasks_will_ping_honeybadger()
    {
        $schedule = $this->app[Schedule::class];

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->once())
            ->method('checkin')
            ->with('1234');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return true;
        })->thenPingHoneybadger('1234');

        $this->artisan('schedule:run');
    }

    /** @test */
    public function scheduled_tasks_will_ping_honeybadger_if_matching_environments()
    {
        $schedule = $this->app[Schedule::class];

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->exactly(2))
            ->method('checkin')
            ->with('1234');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return true;
        })->thenPingHoneybadger('1234', 'testing');

        $schedule->call(function () {
            return true;
        })->thenPingHoneybadger('1234', ['testing', 'production']);

        $this->artisan('schedule:run');
    }

    /** @test */
    public function scheduled_tasks_will_not_ping_honeybadger_if_non_matching_environments()
    {
        $schedule = $this->app[Schedule::class];

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->never())
            ->method('checkin');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return true;
        })->thenPingHoneybadger('1234', 'development');

        $schedule->call(function () {
            return true;
        })->thenPingHoneybadger('1234', ['development', 'production']);

        $this->artisan('schedule:run');
    }

       /** @test */
    public function scheduled_tasks_will_ping_honeybadger_with_id_from_config()
    {
        $schedule = $this->app[Schedule::class];

        Config::set('test_id', '1234');

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->once())
            ->method('checkin')
            ->with('1234');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return true;
        })->thenPingHoneybadgerFromConfig('test_id');

        $this->artisan('schedule:run');
    }

    /** @test */
    public function successful_tasks_will_ping_honeybadger()
    {
        if (version_compare($this->app->version(), '8.6.0', '<')) {
            $this->markTestSkipped("Laravel < 8.6 doesn't set proper return codes for callables.");

            return;
        }

        $schedule = $this->app[Schedule::class];

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->once())
            ->method('checkin')
            ->with('1234');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return true;
        })->pingHoneybadgerOnSuccess('1234');

        $this->artisan('schedule:run');
    }

    /** @test */
    public function successful_tasks_will_ping_honeybadger_if_matching_environments()
    {
        if (version_compare($this->app->version(), '8.6.0', '<')) {
            $this->markTestSkipped("Laravel < 8.6 doesn't set proper return codes for callables.");

            return;
        }

        $schedule = $this->app[Schedule::class];

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->exactly(2))
            ->method('checkin')
            ->with('1234');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return true;
        })->pingHoneybadgerOnSuccess('1234', 'testing');

        $schedule->call(function () {
            return true;
        })->pingHoneybadgerOnSuccess('1234', ['testing', 'production']);

        $this->artisan('schedule:run');
    }

    /** @test */
    public function successful_tasks_will_not_ping_honeybadger_if_non_matching_environments()
    {
        if (version_compare($this->app->version(), '8.6.0', '<')) {
            $this->markTestSkipped("Laravel < 8.6 doesn't set proper return codes for callables.");

            return;
        }

        $schedule = $this->app[Schedule::class];

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->never())
            ->method('checkin');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return true;
        })->pingHoneybadgerOnSuccess('1234', 'development');

        $schedule->call(function () {
            return true;
        })->pingHoneybadgerOnSuccess('1234', ['development', 'production']);

        $this->artisan('schedule:run');
    }

    /** @test */
    public function successful_tasks_will_ping_honeybadger_with_id_from_config()
    {
        if (version_compare($this->app->version(), '8.6.0', '<')) {
            $this->markTestSkipped("Laravel < 8.6 doesn't set proper return codes for callables.");

            return;
        }

        Config::set('test_id', '1234');

        $schedule = $this->app[Schedule::class];

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->once())
            ->method('checkin')
            ->with('1234');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return true;
        })->pingHoneybadgerOnSuccessFromConfig('test_id');

        $this->artisan('schedule:run');
    }
}
