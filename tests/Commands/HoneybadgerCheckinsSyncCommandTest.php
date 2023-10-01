<?php

namespace Honeybadger\Tests\Commands;

use Exception;
use Honeybadger\Contracts\CheckinsSync;
use Honeybadger\Contracts\Reporter;
use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerCheckinCommand;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Config;

class HoneybadgerCheckinsSyncCommandTest extends TestCase
{
    const CHECKINS = [
        [
            'project_id' => 'p1234',
            'name' => 'simple checkin test',
            'scheduleType' => 'simple',
            'report_period' => '1 day',
        ],
        [
            'project_id' => 'p1234',
            'name' => 'cron checkin test',
            'scheduleType' => 'cron',
            'cron_schedule' => '0 * * * *',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('honeybadger', [
            'personal_auth_token' => '1234567890',
            'checkins' => self::CHECKINS,
        ]);
    }

    /** @test */
    public function it_reads_checkins_from_config()
    {
        $mock = $this->createMock(CheckinsSync::class);
        $mock->expects($this->once())
            ->method('sync')
            ->with(self::CHECKINS);

        $this->app->instance(CheckinsSync::class, $mock);

        $this->artisan('honeybadger:checkins-sync');
    }
}
