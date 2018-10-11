<?php

namespace Honeybadger\Tests\Commands;

use Honeybadger\Honeybadger;
use Honeybadger\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Honeybadger\HoneybadgerLaravel\CommandTasks;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer;
use Honeybadger\HoneybadgerLaravel\Commands\SuccessMessage;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerInstallCommand;

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

        $installer->method('publishLaravelConfig')
            ->willReturn(true);

        $this->app[Installer::class] = $installer;

        $commandTasks = new CommandTasks;

        $this->app[CommandTasks::class] = $commandTasks;

        $command = $this->commandMock();

        $command->expects($this->once())
            ->method('requiredSecret')
            ->with('Your API key', 'The API key is required')
            ->willReturn('supersecret');

        $command->expects($this->once())
            ->method('confirm')
            ->with('Would you like to send a test exception now?', true)
            ->willReturn(true);

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
    public function the_correct_config_gets_published_for_lumen()
    {
        $this->app['honeybadger.isLumen'] = true;

        $installer = $this->createMock(Installer::class);

        $installer->method('shouldPublishConfig')
            ->willReturn(true);

        $installer->expects($this->once())
            ->method('publishLumenConfig')
            ->willReturn(true);

        $this->app[Installer::class] = $installer;

        $commandTasks = new CommandTasks;

        $this->app[CommandTasks::class] = $commandTasks;

        $command = $this->commandMock();

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install');

        $this->assertTrue($commandTasks->getResults()['Publish the config file']);
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

        // Send test exception
        $command->method('confirm')
            ->willReturn(false);

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
            ->method('publishLaravelConfig');

        $installer->method('shouldPublishConfig')
            ->willReturn(false);

        $this->app[Installer::class] = $installer;

        $commandTasks = new CommandTasks;
        $this->app[CommandTasks::class] = $commandTasks;

        $command = $this->commandMock();

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

        // Send test exception
        $command->method('confirm')->willReturn(true);

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
        $command->expects($this->never())
            ->method('requiredSecret');

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install', [
            'apiKey' => 'asdf123',
        ]);
    }

    /** @test */
    public function the_success_block_is_output_with_link_to_notice()
    {
        $installer = $this->createMock(Installer::class);

        $installer->method('sendTestException')
            ->willReturn(['id' => '1234']);

        $this->app[Installer::class] = $installer;

        $command = $this->getMockBuilder(HoneybadgerInstallCommand::class)
            ->disableOriginalClone()
            ->setMethods([
                'requiredSecret',
                'confirm',
                'line',
            ])->getMock();

        // Send test exception
        $command->method('confirm')
            ->willReturn(true);

        $command->expects($this->once())
            ->method('line')
            ->with(SuccessMessage::withLinkToNotice('1234'));

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install', [
            'apiKey' => 'asdf123',
        ]);
    }

    /** @test */
    public function correct_success_output_when_failed_honeybadger_request()
    {
        $installer = $this->createMock(Installer::class);

        $installer->method('sendTestException')
            ->willReturn([]);

        $this->app[Installer::class] = $installer;

        $command = $this->getMockBuilder(HoneybadgerInstallCommand::class)
            ->disableOriginalClone()
            ->setMethods([
                'requiredSecret',
                'confirm',
                'line',
            ])->getMock();

        // Send test exception
        $command->method('confirm')
            ->willReturn(true);

        $command->expects($this->once())
            ->method('line')
            ->with(SuccessMessage::withoutLinkToNotices());

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install', [
            'apiKey' => 'asdf123',
        ]);
    }

    /** @test */
    public function the_success_block_is_output_without_link_to_notice()
    {
        $installer = $this->createMock(Installer::class);

        $this->app[Installer::class] = $installer;

        $command = $this->getMockBuilder(HoneybadgerInstallCommand::class)
            ->disableOriginalClone()
            ->setMethods([
                'requiredSecret',
                'confirm',
                'line',
            ])->getMock();

        // Send test exception
        $command->method('confirm')
            ->willReturn(false);

        $command->expects($this->once())
            ->method('line')
            ->with(SuccessMessage::withoutLinkToNotices());

        $this->app[Kernel::class]->registerCommand($command);

        $this->artisan('honeybadger:install', [
            'apiKey' => 'asdf123',
        ]);
    }

    private function commandMock()
    {
        return $this->getMockBuilder(HoneybadgerInstallCommand::class)
            ->disableOriginalClone()
            ->setMethods([
                'requiredSecret',
                'line',
                'confirm',
            ])->getMock();
    }
}
