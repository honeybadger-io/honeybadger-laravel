<?php

namespace Honeybadger\Tests\Commands;

use Exception;
use Honeybadger\Honeybadger;
use Honeybadger\Tests\TestCase;
use Honeybadger\Contracts\Reporter;
use Illuminate\Contracts\Console\Kernel;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerCheckinCommand;

class HoneybadgerCheckinCommandTest extends TestCase
{
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

        $command = $this->getMockBuilder(HoneybadgerCheckinCommand::class)
            ->disableOriginalClone()
            ->setMethods(['info'])
            ->getMock();

        $command->expects($this->once())
            ->method('info')
            ->with('Checkin 1234 was sent to Honeybadger');

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

        $command = $this->getMockBuilder(HoneybadgerCheckinCommand::class)
            ->disableOriginalClone()
            ->setMethods(['error'])
            ->getMock();

        $command->expects($this->once())
                ->method('error')
                ->with('Some message');

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:checkin', ['id' => '1234']);
    }
}
