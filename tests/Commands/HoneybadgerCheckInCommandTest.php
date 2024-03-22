<?php

namespace Honeybadger\Tests\Commands;

use Exception;
use Honeybadger\Contracts\Reporter;
use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerCheckInCommand;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Config;

class HoneybadgerCheckInCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('honeybadger.report_data', true);
    }

    /** @test */
    public function it_sends_a_test_exception_to_honeybadger()
    {
        $mock = $this->createMock(Reporter::class);
        $mock->expects($this->once())
            ->method('checkin')
            ->with('1234');

        $this->app->instance(Reporter::class, $mock);

        $this->artisan('honeybadger:checkin', ['id' => '1234']);
    }

    /** @test */
    public function it_outputs_success()
    {
        $mock = $this->createMock(Reporter::class);
        $this->app->instance(Honeybadger::class, $mock);

        $command = $this->getMockBuilder(HoneybadgerCheckInCommand::class)
            ->disableOriginalClone()
            ->onlyMethods(['info'])
            ->getMock();

        $command->expects($this->once())
            ->method('info')
            ->with('Check-in 1234 was sent to Honeybadger');

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:checkin', ['id' => '1234']);
    }

    /** @test */
    public function it_outputs_an_error()
    {
        $mock = $this->createMock(Reporter::class);
        $mock->method('checkin')
            ->will($this->throwException(new Exception('Some message')));

        $this->app->instance(Reporter::class, $mock);

        $command = $this->getMockBuilder(HoneybadgerCheckInCommand::class)
            ->disableOriginalClone()
            ->onlyMethods(['error'])
            ->getMock();

        $command->expects($this->once())
                ->method('error')
                ->with('Some message');

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:checkin', ['id' => '1234']);
    }
}
