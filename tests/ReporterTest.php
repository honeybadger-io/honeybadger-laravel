<?php

namespace Honeybadger\Tests;

use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerClient;
use Honeybadger\HoneybadgerLaravel\HoneybadgerLaravel;
use Honeybadger\Tests\Fixtures\TestController;
use Illuminate\Support\Facades\Route;

class ReporterTest extends TestCase
{
    /** @test */
    public function it_automatically_adds_the_user_context()
    {
        request()->setUserResolver(function () {
            return new class {
                public function getAuthIdentifier()
                {
                    return '1234';
                }
            };
        });
        $client = $this->createMock(HoneybadgerClient::class);
        $client->expects($this->once())
            ->method('notification')
            ->withAnyParameters()
            ->willReturnCallback(function ($notification) {
                $this->assertEquals(['user_id' => '1234'], $notification['request']['context']);

                return ['id' => 'ojuih86747909i6511c'];
            });

        $badger = HoneybadgerLaravel::new([
            'api_key' => 'asdf',
            'handlers' => [
                'exception' => false,
                'error' => false,
            ],
        ]);
        $reflectedBadger = new \ReflectionClass($badger);
        $reflectedClient = $reflectedBadger->getProperty('client');
        $reflectedClient->setAccessible(true);
        $reflectedClient->setValue($badger, $client);
        $this->app[Reporter::class] = $badger;

        $badger->notify(new \Exception('Test exception'));
    }

    /** @test */
    public function it_automatically_sets_action_and_context()
    {
        request()->setUserResolver(function () {
            return new class {
                public function getAuthIdentifier()
                {
                    return '1234';
                }
            };
        });
        $client = $this->createMock(HoneybadgerClient::class);
        $client->expects($this->once())
            ->method('notification')
            ->withAnyParameters()
            ->willReturnCallback(function ($notification) {
                $this->assertEquals('Honeybadger\Tests\Fixtures\TestController', $notification['request']['component']);
                $this->assertEquals('recordException', $notification['request']['action']);

                return ['id' => 'ojuih86747909i6511c'];
            });

        $badger = HoneybadgerLaravel::new([
            'api_key' => 'asdf',
            'handlers' => [
                'exception' => false,
                'error' => false,
            ],
        ]);
        $reflectedBadger = new \ReflectionClass($badger);
        $reflectedClient = $reflectedBadger->getProperty('client');
        $reflectedClient->setAccessible(true);
        $reflectedClient->setValue($badger, $client);
        $this->app[Reporter::class] = $badger;

        Route::get('/recordException', [TestController::class, 'recordException']);

        $this->get('/recordException');
    }

}
