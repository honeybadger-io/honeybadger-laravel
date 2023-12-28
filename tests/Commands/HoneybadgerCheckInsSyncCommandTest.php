<?php

namespace Honeybadger\Tests\Commands;

use Honeybadger\Contracts\SyncCheckIns;
use Honeybadger\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class HoneybadgerCheckInsSyncCommandTest extends TestCase
{
    const CHECKINS = [
        [
            'slug' => 'simple-checkin-test',
            'scheduleType' => 'simple',
            'report_period' => '1 day',
        ],
        [
            'slug' => 'cron-checkin-test',
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
    public function it_reads_check_ins_from_config()
    {
        $mock = $this->createMock(SyncCheckIns::class);
        $mock->expects($this->once())
            ->method('sync')
            ->with(self::CHECKINS);

        $this->app->instance(SyncCheckIns::class, $mock);

        $this->artisan('honeybadger:checkins:sync');
    }
}
