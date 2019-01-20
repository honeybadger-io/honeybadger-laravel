<?php

namespace Honeybadger\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Log;
use Honeybadger\Contracts\Reporter;
use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\HoneybadgerLogDriver;
use Honeybadger\LogHandler;

class LogDriverTest extends TestCase
{
    /** @test */
    public function the_log_driver_can_be_correctly_registered_and_used()
    {
        $this->app['config']->set('logging.channels.honeybadger', [
            'driver'  => 'custom',
            'via' => HoneybadgerLogDriver::class,
            'name' => 'asdf' // optional point of customization
        ]);

        $logHandler = $this->getMockBuilder(LogHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['write'])
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
