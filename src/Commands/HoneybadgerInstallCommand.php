<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Honeybadger\Exceptions\ServiceException;
use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\CommandTasks;
use Honeybadger\HoneybadgerLaravel\Concerns\RequiredInput;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer;
use Honeybadger\HoneybadgerLaravel\Exceptions\TaskFailed;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class HoneybadgerInstallCommand extends Command
{
    use RequiredInput;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'honeybadger:install {apiKey?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and configure Honeybadger';

    /**
     * Configuration from gathered input.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var \Honeybadger\HoneybadgerLaravel\Contracts\Installer;
     */
    protected $installer;

    /**
     * @var \Honeybadger\HoneybadgerLaravel\CommandTasks
     */
    protected $tasks;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Installer $installer, CommandTasks $commandTasks)
    {
        $this->installer = $installer;
        $this->tasks = $commandTasks;
        $this->tasks->setOutput($this->output);

        $this->config = $this->gatherConfig();

        $this->writeEnv();

        if ($this->installer->shouldPublishConfig()) {
            $this->tasks->addTask(
                'Publish the config file',
                function () {
                    return $this->publishConfig();
                }
            );
        }

        $this->addTestExceptionTask();

        try {
            $this->tasks->runTasks();
            $results = $this->tasks->getResults();
            $testExceptionResult = end($results);
            $this->outputSuccessMessage(Arr::get($testExceptionResult, 'id', ''));
        } catch (TaskFailed $e) {
            $this->line('');
            $this->error($e->getMessage());
        }
    }

    /**
     * Prompt for input and gather responses.
     *
     * @return array
     */
    private function gatherConfig(): array
    {
        return [
            'api_key' => $this->argument('apiKey') ?? $this->promptForApiKey(),
        ];
    }

    /**
     * Prompt for the API key.
     *
     * @return string
     */
    private function promptForApiKey(): string
    {
        return $this->requiredSecret('Your API key', 'The API key is required');
    }

    /**
     * Send test exception to Honeybadger.
     */
    private function addTestExceptionTask(): void
    {
        Config::set('honeybadger.api_key', $this->config['api_key']);

        $this->tasks->addTask(
            'Send test exception to Honeybadger',
            function () {
                $result = $this->installer->sendTestException();
                return empty($result) ? false : $result;
            },
            true
        );
    }

    /**
     * Write configuration values to the env files.
     *
     * @return void
     */
    private function writeEnv(): void
    {
        $this->tasks->addTask(
            'Write HONEYBADGER_API_KEY to .env',
            function () {
                return $this->installer->writeConfig(
                    ['HONEYBADGER_API_KEY' => $this->config['api_key']],
                    base_path('.env')
                );
            }
        );

        $this->tasks->addTask(
            'Write HONEYBADGER_API_KEY placeholder to .env.example',
            function () {
                return $this->installer->writeConfig(
                    ['HONEYBADGER_API_KEY' => ''],
                    base_path('.env.example')
                );
            }
        );

        $this->tasks->addTask(
            'Write HONEYBADGER_VERIFY_SSL placeholder to .env.example',
            function () {
                return $this->installer->writeConfig(
                    ['HONEYBADGER_VERIFY_SSL' => ''],
                    base_path('.env.example')
                );
            }
        );
    }

    /**
     * Publish the config file for Lumen or Laravel.
     *
     * @return bool
     */
    public function publishConfig(): bool
    {
        if (app('honeybadger.isLumen')) {
            return $this->installer->publishLumenConfig();
        }

        return $this->installer->publishLaravelConfig();
    }

    /**
     * Output the success message.
     *
     * @param  string  $noticeId
     * @return void
     */
    private function outputSuccessMessage(string $noticeId): void
    {
        $this->line(SuccessMessage::make($noticeId));
    }
}
