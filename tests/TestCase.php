<?php

namespace Honeybadger\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            HoneybadgerServiceProvider::class,
            AuthServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('honeybadger.api_key', 'asdf');
    }
}
