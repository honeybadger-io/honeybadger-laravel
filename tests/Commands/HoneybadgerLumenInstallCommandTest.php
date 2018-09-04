<?php

namespace Honeybadger\Tests\Commands;

use Mockery;
use Honeybadger\Honeybadger;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;

class HoneybadgerLumenInstallCommandTest extends TestCase
{
    public function setUp()
    {
        parent::setup();

        $this->app->setBasePath(__DIR__.'/tmp/lumen');
        $this->app['honeybadger.isLumen'] = true;

        $this->app->getProvider(HoneybadgerServiceProvider::class)->boot();
    }

    /** @test */
    public function publish_does_not_run_if_config_file_exists()
    {
        mkdir(__DIR__.'/tmp/lumen/config');
        touch(__DIR__.'/tmp/lumen/config/honeybadger.php');

        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerLumenInstallCommand[confirm,requiredSecret,task]');

        // API key
        $command->shouldReceive('requiredSecret')->once();

        // Send test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $command->shouldNotReceive('task')->with('Publishing the config file', true);
        $command->shouldReceive('task')->times(2);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');

        unlink(__DIR__.'/tmp/lumen/config/honeybadger.php');
        rmdir(__DIR__.'/tmp/lumen/config');
    }

    /** @test */
    public function the_config_is_published()
    {
        // So we manually need to manually create the directory and copy
        // the configuration if its lumen
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerLumenInstallCommand[confirm,requiredSecret]');

        // API key
        $command->shouldReceive('requiredSecret')->once();

        // Send test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');

        $this->assertEquals(
            file_get_contents(__DIR__.'/../../config/honeybadger.php'),
            file_get_contents(__DIR__.'/tmp/lumen/config/honeybadger.php')
        );
        $this->assertTrue(is_dir(__DIR__.'/tmp/lumen/config'));

        unlink(__DIR__.'/tmp/lumen/config/honeybadger.php');
        rmdir(__DIR__.'/tmp/lumen/config');
    }
}
