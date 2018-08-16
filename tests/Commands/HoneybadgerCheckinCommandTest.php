<?php

namespace Honeybadger\Tests\Commands;

use Mockery;
use Exception;
use Honeybadger\Honeybadger;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;

class HoneybadgerCheckinCommandTest extends TestCase
{
    /** @test */
    public function it_sends_a_test_exception_to_honeybadger()
    {
        $mock = Mockery::mock(Honeybadger::class)
            ->makePartial()
            ->shouldReceive('checkin')
            ->once()
            ->with('1234')
            ->getMock();

        $this->app->instance(Honeybadger::class, $mock);

        $this->artisan('honeybadger:checkin', ['id' => '1234']);
    }

    /** @test */
    public function it_outputs_success()
    {
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerCheckinCommand[info]')
            ->shouldReceive('info')
            ->once()
            ->with('Checkin 1234 was sent to Honeybadger')
            ->getMock();

        $this->app[Kernel::class]->registerCommand($command);

        $mock = Mockery::mock(Honeybadger::class)
            ->makePartial()
            ->shouldReceive('checkin')
            ->getMock();

        $this->app->instance(Honeybadger::class, $mock);
        $this->artisan('honeybadger:checkin', ['id' => '1234']);
    }

    /** @test */
    public function it_outputs_an_error()
    {
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerCheckinCommand[error]')
            ->shouldReceive('error')
            ->once()
            ->with('An error occured')
            ->getMock();

        $this->app[Kernel::class]->registerCommand($command);

        $mock = Mockery::mock(Honeybadger::class)
            ->makePartial()
            ->shouldReceive('checkin')
            ->andThrow(new Exception('An error occured'))
            ->getMock();

        $this->app->instance(Honeybadger::class, $mock);

        $this->artisan('honeybadger:checkin', ['id' => '1234']);
    }
}
