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
            return true;
        })->thenPingHoneybadger('1234');

        $this->artisan('schedule:run');
    }
}
