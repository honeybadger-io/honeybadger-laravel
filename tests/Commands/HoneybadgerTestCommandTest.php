<?php

namespace Honeybadger\Tests\Commands;

use Mockery;
use Exception;
use Honeybadger\Tests\TestCase;
use Honeybadger\Contracts\Reporter;
use Illuminate\Contracts\Console\Kernel;
use Honeybadger\HoneybadgerLaravel\Exceptions\TestException;

class HoneybadgerTestCommandTest extends TestCase
{
    /** @test */
    public function it_sends_a_test_exception_to_honeybadger()
    {
        $mock = $this->createMock(Reporter::class);
        $mock->expects($this->once())
            ->method('notify')
            ->with($this->isInstanceOf(TestException::class));

        $this->app->instance(Reporter::class, $mock);

        $this->artisan('honeybadger:test');
    }

    /** @test */
    public function it_outputs_success()
    {
        $mock = $this->createMock(Reporter::class);
        $mock->method('notify')
            ->willReturn([]);

        $this->app->instance(Reporter::class, $mock);

        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerTestCommand[info]')
            ->shouldReceive('info')
            ->once()
            ->with('A test exception was sent to Honeybadger')
            ->getMock();

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:test');
    }

    /** @test */
    public function it_outputs_an_error()
    {
        $mock = $this->createMock(Reporter::class);
        $mock->method('notify')
            ->will($this->throwException(new Exception('An error occured')));

        $this->app->instance(Reporter::class, $mock);

        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerTestCommand[error]')
            ->shouldReceive('error')
            ->once()
            ->with('An error occured')
            ->getMock();

        $this->app[Kernel::class]->registerCommand($command);

        $this->app->instance(Honeybadger::class, $mock);

        $this->artisan('honeybadger:test');
    }
}
