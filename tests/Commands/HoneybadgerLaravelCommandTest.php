<?php

namespace Honeybadger\Tests\Commands;

use Mockery;
use Honeybadger\Honeybadger;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;

class HoneybadgerLaravelCommandTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app->setBasePath(__DIR__.'/tmp');

        array_map('touch', [
            __DIR__.'/tmp/.env',
            __DIR__.'/tmp/.env.example',
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();

        try {
            array_map('unlink', [
                __DIR__.'/tmp/.env',
                __DIR__.'/tmp/.env.example',
                __DIR__.'/tmp/config/honeybadger.php',
            ]);
        } catch (\Exception $e) {
            // swallow
        }
    }

    /** @test */
    public function the_config_gets_published()
    {
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerLaravelInstallCommand[confirm,secret,task,callSilent]');

        // API key
        $command->shouldReceive('secret')->once();

        // Send test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $command->shouldReceive('task')->times(2);

        $command->shouldReceive('task')
            ->once()
            ->with('Publish the config file', true);

        $command->shouldReceive('callSilent')
            ->with('vendor:publish', [
                '--provider' => HoneybadgerServiceProvider::class,
            ])->andReturn(0);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');
    }
}
