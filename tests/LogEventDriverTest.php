<?php

namespace Honeybadger\Tests;

use Honeybadger\Contracts\Reporter;
use Honeybadger\LogEventHandler;
use Illuminate\Support\Facades\Log;
use Honeybadger\HoneybadgerLaravel\HoneybadgerLogEventDriver;
use PHPUnit\Framework\MockObject\Exception;

class LogEventDriverTest extends TestCase
{
    /** @test
     * @throws Exception
     */
    public function the_log_driver_can_be_correctly_registered_and_used()
    {
        $this->app['config']->set('logging.channels.honeybadger', [
            'driver' => 'custom',
            'via' => HoneybadgerLogEventDriver::class,
            'name' => 'asdf', // optional point of customization
        ]);

        $reporter = $this->createMock(Reporter::class);

        $logHandler = new LogEventHandler($reporter);

        $reporter->expects($this->once())
            ->method('event')
            ->with('log', [
                'ts' => (new \DateTime())->format(DATE_ATOM),
                'severity' => 'info',
                'message' => 'Test message',
                'channel' => 'asdf',
            ]);

        $this->app[LogEventHandler::class] = $logHandler;

        Log::channel('honeybadger')->info('Test message');
    }
}
