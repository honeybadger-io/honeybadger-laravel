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

        $reporter = $this->getMockReporter();
        $logHandler = new LogEventHandler($reporter);

        $this->app[LogEventHandler::class] = $logHandler;

        Log::channel('honeybadger')->info('Test message');

        $events = $reporter->getEvents();
        $this->assertCount(1, $events);

        $event = $events[0];
        $this->assertArrayHasKey('ts', $event[1]);
        $this->assertEquals('log', $event[0]);
        $this->assertEquals('info', $event[1]['severity']);
        $this->assertEquals('Test message', $event[1]['message']);
        $this->assertEquals('asdf', $event[1]['channel']);
    }

    private function getMockReporter(): Reporter {
        return new class implements Reporter {
            private array $events = [];

            public function notify($throwable, $request = null, array $additionalParams = []): array
            {
                return [];
            }

            public function customNotification(array $payload): array
            {
                return [];
            }

            public function rawNotification(callable $callable): array
            {
                return [];
            }

            public function checkin(string $idOrSlug): void
            {
            }

            public function context($key, $value = null)
            {
            }

            public function addBreadcrumb(string $message, array $metadata = [], string $category = 'custom'): Reporter
            {
                return $this;
            }

            public function clear(): Reporter
            {
                return $this;
            }

            public function event($eventTypeOrPayload, array $payload = null): void
            {
                $this->events[] = [$eventTypeOrPayload, $payload];
            }

            public function flushEvents(): void
            {
                $this->events = [];
            }

            /**
             * Accessor method to the events array.
             *
             * @return array
             */
            public function getEvents(): array
            {
                return $this->events;
            }
        };
    }


}
