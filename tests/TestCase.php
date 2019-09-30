<?php

namespace Honeybadger\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [HoneybadgerServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('honeybadger.api_key', 'asdf');
    }
}
