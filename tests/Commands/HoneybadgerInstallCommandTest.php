<?php

namespace Honeybadger\Tests\Commands;

use Mockery;
use Honeybadger\Honeybadger;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Honeybadger\HoneybadgerLaravel\CommandTasks;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer;

class HoneybadgerInstallCommandTest extends TestCase
{
    /** @test */
    public function prompts_for_options_and_outputs_all_successful_operations()
    {
        $installer = $this->createMock(Installer::class);
        $installer->method('sendTestException')
            ->willReturn(['id' => '1234']);

        $installer->method('writeConfig')
            ->willReturn(true);

        $installer->method('shouldPublishConfig')
            ->willReturn(true);

        $installer->method('publishConfig')
            ->willReturn(true);

        $this->app[Installer::class] =  $installer;

        $commandTasks = new CommandTasks;

        $this->app[CommandTasks::class] = $commandTasks;

        $command = $this->commandMock();

        $command->shouldReceive('requiredSecret')
            ->with('Your API key', 'The API key is required')
            ->andReturn('supersecret');

        $command->shouldReceive('confirm')
            ->once()
            ->with('Would you like to send a test exception now?', true)
            ->andReturn(true);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');

        $this->assertEquals([
            'Write HONEYBADGER_API_KEY to .env' => true,
            'Write HONEYBADGER_API_KEY placeholder to .env.example' => true,
            'Publish the config file' => true,
            'Send test exception to Honeybadger' => true,
        ], $commandTasks->getResults());
    }

    /** @test */
    public function test_exception_does_not_get_sent_based_on_input()
    {
        $installer = $this->createMock(Installer::class);

        $installer->expects($this->never())
            ->method('sendTestException');

        $this->app[Installer::class] = $installer;

        $commandTasks = new CommandTasks;
        $this->app[CommandTasks::class] = $commandTasks;

        $command = $this->commandMock();

        // API key
        $command->shouldReceive('requiredSecret')->once();

        // Send test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');

        $this->assertArrayNotHasKey(
            'Send test exception to Honeybadger',
            $commandTasks->getResults()
        );
    }

    /** @test */
    public function publish_does_not_run_if_config_file_exists()
    {
        $installer = $this->createMock(Installer::class);

        $installer->expects($this->never())
            ->method('publishConfig');

        $installer->method('shouldPublishConfig')
            ->willReturn(false);

        $this->app[Installer::class] = $installer;

        $commandTasks = new CommandTasks;
        $this->app[CommandTasks::class] = $commandTasks;

        $command = $this->commandMock();

        // API key
        $command->shouldReceive('requiredSecret')->once();

        // Send test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');

        $this->assertArrayNotHasKey(
            'Publish the config file',
            $commandTasks->getResults()
        );
    }

    /** @test */
    public function sends_a_test_to_honeybadger()
    {
        $installer = $this->createMock(Installer::class);

        $installer->expects($this->once())
            ->method('sendTestException')
            ->willReturn(['id' => '1234']);

        $this->app[Installer::class] = $installer;

        $commandTasks = new CommandTasks;
        $this->app[CommandTasks::class] = $commandTasks;

        $command = $this->commandMock();

        // API key
        $command->shouldReceive('requiredSecret')->once()->andReturn('supersecret');

        // Send test exception
        $command->shouldReceive('confirm')->once()->andReturn(true);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');

        $this->assertTrue($commandTasks->getResults()['Send test exception to Honeybadger']);
    }

    /** @test */
    public function gracefully_handles_env_file_not_existing()
    {
        $installer = $this->createMock(Installer::class);

        $installer->method('writeConfig')
            ->willReturn(false);

        $this->app[Installer::class] = $installer;

        $commandTasks = new CommandTasks;
        $this->app[CommandTasks::class] = $commandTasks;

        $command = $this->commandMock();

        // API key
        $command->shouldReceive('requiredSecret')->andReturn('supersecret');

        // Test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');

        $taskResults = $commandTasks->getResults();

        $this->assertFalse($taskResults['Write HONEYBADGER_API_KEY to .env']);
        $this->assertFalse($taskResults['Write HONEYBADGER_API_KEY placeholder to .env.example']);
    }

    /** @test */
    public function prompt_for_api_keys_does_not_get_called_if_key_is_passed()
    {
        $this->app[Installer::class] = $this->createMock(Installer::class);

        $command = $this->commandMock();

        // API key
        $command->shouldNotReceive('requiredSecret');

        // Test exception
        $command->shouldReceive('confirm')->once()->andReturn(false);

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install', [
            'apiKey' => 'asdf123',
        ]);
    }

    private function commandMock()
    {
        return Mockery::mock('Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand[confirm,requiredSecret]');
    }
}
