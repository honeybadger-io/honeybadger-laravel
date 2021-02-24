<?php

namespace Honeybadger\Tests;

use Honeybadger\Contracts\Reporter;
use Illuminate\Console\Scheduling\Schedule;

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
            return 0;
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
            return 0;
        })->thenPingHoneybadger('1234', 'testing');

        $schedule->call(function () {
            return 0;
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
            return 0;
        })->thenPingHoneybadger('1234', 'development');

        $schedule->call(function () {
            return 0;
        })->thenPingHoneybadger('1234', ['development', 'production']);

        $this->artisan('schedule:run');
    }

    /** @test */
    public function successful_tasks_will_ping_honeybadger_if_matching_environments()
    {
        $schedule = $this->app[Schedule::class];

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->exactly(2))
            ->method('checkin')
            ->with('1234');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return 0;
        })->pingHoneybadgerOnSuccess('1234', 'testing');

        $schedule->call(function () {
            return 0;
        })->pingHoneybadgerOnSuccess('1234', ['testing', 'production']);

        $this->artisan('schedule:run');
    }

    /** @test */
    public function successful_tasks_will_not_ping_honeybadger_if_non_matching_environments()
    {
        $schedule = $this->app[Schedule::class];

        $honeybadger = $this->createMock(Reporter::class);
        $honeybadger->expects($this->never())
            ->method('checkin');

        $this->app->instance(Reporter::class, $honeybadger);

        $schedule->call(function () {
            return 0;
        })->pingHoneybadgerOnSuccess('1234', 'development');

        $schedule->call(function () {
            return 0;
        })->pingHoneybadgerOnSuccess('1234', ['development', 'production']);

        $this->artisan('schedule:run');
    }
}
