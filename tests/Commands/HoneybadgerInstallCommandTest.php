<?php

namespace Honeybadger\Tests\Commands;

use Mockery;
use Honeybadger\Honeybadger;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;

class HoneybadgerInstallCommandTest extends TestCase
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
    public function prompts_for_options_and_outputs_all_successful_operations()
    {
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand[confirm,secret,task,callSilent]');

        $command->shouldReceive('secret')
            ->once()
            ->with('Please enter your API key')
            ->andReturn('supersecret');

        $command->shouldReceive('confirm')
            ->once()
            ->with('Would you like to send a test exception now?', true)
            ->andReturn(true);

        $command->shouldReceive('task')
            ->once()
            ->with('Write HONEYBADGER_API_KEY to .env', true);

        $command->shouldReceive('task')
            ->once()
            ->with('Write HONEYBADGER_API_KEY placeholder to .env.example', true);

        $command->shouldreceive('task')
            ->once()
            ->with('Publish the config file', true);

        $command->shouldReceive('task')
            ->once()
            ->with('Send test exception to Honeybadger', true);

        $command->shouldReceive('callSilent')->times(1)->andReturn(0);
        $command->shouldReceive('publishConfig')->andReturn(true);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');
    }

    /** @test */
    public function env_configartions_are_written()
    {
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand[confirm,secret,task]');

        // API key
        $command->shouldReceive('secret')->andReturn('supersecret');

        // Test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $command->shouldReceive('task')
            ->once()
            ->with('Write HONEYBADGER_API_KEY to .env', true);

        $command->shouldReceive('task')
            ->once()
            ->with('Write HONEYBADGER_API_KEY placeholder to .env.example', true);

        // Remaining tasks
        $command->shouldReceive('task')->once();

        $command->shouldReceive('publishConfig');

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');

        $this->assertEquals('HONEYBADGER_API_KEY=supersecret', file_get_contents(__DIR__.'/tmp/.env'));
        $this->assertEquals('HONEYBADGER_API_KEY=', file_get_contents(__DIR__.'/tmp/.env.example'));
    }

    /** @test */
    public function sending_test_exception_does_not_run_based_on_input()
    {
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand[confirm,secret,task,callSilent]');

        // API key
        $command->shouldReceive('secret')->once();

        // Send test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $command->shouldReceive('callSilent')
            ->with('vendor:publish', Mockery::any());

        $command->shouldNotReceive('callSilent')
            ->with('honeybadger:test');

        $command->shouldReceive('task')->times(3);

        $command->shouldReceive('publishConfig');

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');
    }

    /** @test */
    public function publish_does_not_run_if_config_file_exists()
    {
        touch(__DIR__.'/tmp/config/honeybadger.php');

        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand[confirm,secret,task]');

        // API key
        $command->shouldReceive('secret')->once();

        // Send test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $command->shouldReceive('task')->times(2);

        $command->shouldNotReceive('task')
            ->with('Publish the config file', false);

        $command->shouldNotReceive('publishConfig');

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');
    }

    /** @test */
    public function sends_a_test_to_honeybadger()
    {
        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand[confirm,secret,task,callSilent]');

        // API key
        $command->shouldReceive('secret')->once()->andReturn('supersecret');

        // Send test exception
        $command->shouldReceive('confirm')->once()->andReturn(true);

        $command->shouldReceive('task')->times(3);

        $command->shouldReceive('task')
            ->once()
            ->with('Send test exception to Honeybadger', true);

        $command->shouldReceive('publishConfig');

        $command->shouldReceive('callSilent')
            ->once()
            ->with('honeybadger:test')
            ->andReturn(0);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');
    }

    /** @test */
    public function gracefully_handles_env_file_not_existing()
    {
        array_map('unlink', [
            __DIR__.'/tmp/.env',
            __DIR__.'/tmp/.env.example',
        ]);

        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand[confirm,secret,task]');

        // API key
        $command->shouldReceive('secret')->andReturn('supersecret');

        // Test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $command->shouldReceive('task')
            ->once()
            ->with('Write HONEYBADGER_API_KEY to .env', false);

        $command->shouldReceive('task')
            ->once()
            ->with('Write HONEYBADGER_API_KEY placeholder to .env.example', false);

        $command->shouldReceive('publishConfig');

        // Remaining tasks
        $command->shouldReceive('task')->once();

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');
    }

    /** @test */
    public function prompt_for_api_keys_does_not_get_called_if_key_is_passed()
    {
        array_map('unlink', [
            __DIR__.'/tmp/.env',
            __DIR__.'/tmp/.env.example',
        ]);

        $command = Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand[confirm,secret,task]');

        // API key
        $command->shouldNotReceive('secret');

        // Test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $command->shouldReceive('publishConfig');

        // Remaining tasks
        $command->shouldReceive('task')->times(3);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install', [
            'apiKey' => 'asdf123',
        ]);
    }
}
