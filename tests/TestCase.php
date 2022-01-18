<?php

namespace Honeybadger\Tests;

use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

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

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(\Illuminate\Contracts\Console\Kernel::class, ConsoleKernel::class);
    }
}
