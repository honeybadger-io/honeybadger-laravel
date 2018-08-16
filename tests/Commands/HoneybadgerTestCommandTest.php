<?php

namespace Honeybadger\Tests\Commands;

use Mockery;
use Exception;
use Honeybadger\Honeybadger;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Honeybadger\HoneybadgerLaravel\Exceptions\TestException;

class HoneybadgerTestCommandTest extends TestCase
{
    /** @test */
    public function it_sends_a_test_exception_to_honeybadger()
    {
        $mock = Mockery::mock(Honeybadger::class)
            ->makePartial()
            ->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function ($argument) {
                return $argument instanceof TestException
                    && $argument->getMessage() === 'This is an example exception for Honeybadger';
            }))
            ->getMock();

        $this->app->instance(Honeybadger::class, $mock);

        $this->artisan('honeybadger:test');
    }

    /** @test */
    public function it_outputs_success()
    {
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerTestCommand[info]')
            ->shouldReceive('info')
            ->once()
            ->with('A test exception was sent to Honeybadger')
            ->getMock();

        $this->app[Kernel::class]->registerCommand($command);

        $mock = Mockery::mock(Honeybadger::class)
            ->makePartial()
            ->shouldReceive('notify')
            ->getMock();

        $this->app->instance(Honeybadger::class, $mock);

        $this->artisan('honeybadger:test');
    }

    /** @test */
    public function it_outputs_an_error()
    {
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerTestCommand[error]')
            ->shouldReceive('error')
            ->once()
            ->with('An error occured')
            ->getMock();

        $this->app[Kernel::class]->registerCommand($command);

        $mock = Mockery::mock(Honeybadger::class)
            ->makePartial()
            ->shouldReceive('notify')
            ->andThrow(new Exception('An error occured'))
            ->getMock();

        $this->app->instance(Honeybadger::class, $mock);

        $this->artisan('honeybadger:test');
    }
}
