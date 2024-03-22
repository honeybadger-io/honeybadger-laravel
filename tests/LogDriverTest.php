<?php

namespace Honeybadger\Tests;

use Illuminate\Support\Facades\Log;
use Honeybadger\HoneybadgerLaravel\HoneybadgerLogDriver;
use Honeybadger\LogHandler;

class LogDriverTest extends TestCase
{
    /** @test */
    public function the_log_driver_can_be_correctly_registered_and_used()
    {
        $this->app['config']->set('logging.channels.honeybadger', [
            'driver' => 'custom',
            'via' => HoneybadgerLogDriver::class,
            'name' => 'asdf', // optional point of customization
        ]);

        $logHandler = $this->getMockBuilder(LogHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['write'])
            ->getMock();

        $logHandler->expects($this->once())
                   ->method('write')
                   ->with($this->callback(function ($record) {
                       return $record['message'] === 'Test message'
                           && $record['level_name'] === 'INFO'
                           && $record['channel'] === 'asdf';
                   }));

        $this->app[LogHandler::class] = $logHandler;

        Log::channel('honeybadger')->info('Test message');
    }
}
