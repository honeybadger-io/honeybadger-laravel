<?php

namespace Honeybadger\Tests;

use Mockery;
use Honeybadger\Honeybadger;
use Illuminate\Console\Scheduling\Schedule;

class HoneybadgerEventPingTest extends TestCase
{
    /** @test */
    public function scheduled_tasks_will_ping_honeybadger()
    {
        $schedule = app(Schedule::class);

        $honeybadger = Mockery::mock(Honeybadger::class)->makePartial();
        $honeybadger->shouldReceive('checkin')->once()->with('1234');

        $this->app->instance(Honeybadger::class, $honeybadger);

        $schedule->call(function () {
            return true;
        })->thenPingHoneybadger('1234');

        $this->artisan('schedule:run');
    }
}
