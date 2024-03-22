<?php

namespace Honeybadger\Tests\Commands;

use Exception;
use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerTestCommand;
use Honeybadger\HoneybadgerLaravel\Exceptions\TestException;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Config;

class HoneybadgerTestCommandTest extends TestCase
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
            ->method('notify')
            ->with($this->isInstanceOf(TestException::class));

        $this->app->instance('honeybadger.loud', $mock);

        $this->artisan('honeybadger:test');
    }

    /** @test */
    public function it_outputs_success()
    {
        $mock = $this->createMock(Reporter::class);
        $mock->method('notify')
            ->willReturn(['id' => 8633]);

        $this->app->instance('honeybadger.loud', $mock);

        $command = $this->getMockBuilder(HoneybadgerTestCommand::class)
            ->disableOriginalClone()
            ->onlyMethods(['info'])
            ->getMock();

        $command->expects($this->once())
            ->method('info')
            ->with('Successfully sent a test exception to Honeybadger: https://app.honeybadger.io/notice/8633');

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:test');
    }

    /** @test */
    public function it_outputs_an_error()
    {
        $mock = $this->createMock(Reporter::class);
        $mock->method('notify')
            ->will($this->throwException(new Exception('An error occurred')));

        $this->app->instance('honeybadger.loud', $mock);

        $command = $this->getMockBuilder(HoneybadgerTestCommand::class)
            ->disableOriginalClone()
            ->onlyMethods(['error'])
            ->getMock();

        $command->expects($this->once())
            ->method('error')
            ->with('An error occurred');

        $this->app[Kernel::class]->registerCommand($command);

        $this->app->instance('honeybadger.loud', $mock);

        $this->artisan('honeybadger:test');
    }

    /** @test */
    public function it_outputs_an_error_based_on_honeybadger_response()
    {
        $mock = $this->createMock(Reporter::class);
        $mock->method('notify')
            ->willReturn([]);

        $this->app->instance('honeybadger.loud', $mock);

        $command = $this->getMockBuilder(HoneybadgerTestCommand::class)
            ->disableOriginalClone()
            ->onlyMethods(['error'])
            ->getMock();

        $command->expects($this->once())
            ->method('error')
            ->with('There was an error sending the exception to Honeybadger');

        $this->app[Kernel::class]->registerCommand($command);

        $this->app->instance('honeybadger.loud', $mock);

        $this->artisan('honeybadger:test');
    }
}
