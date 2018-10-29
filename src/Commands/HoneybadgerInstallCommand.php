<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Honeybadger\Honeybadger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Honeybadger\Exceptions\ServiceException;
use Honeybadger\HoneybadgerLaravel\CommandTasks;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer;
use Honeybadger\HoneybadgerLaravel\Exceptions\TaskFailed;
use Honeybadger\HoneybadgerLaravel\Concerns\RequiredInput;

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

        $results = $this->sendTest();

        try {
            $this->tasks->runTasks();
            $this->outputSuccessMessage(array_get($results ?? [], 'id', ''));
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
    private function gatherConfig() : array
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
    private function promptForApiKey() : string
    {
        return $this->requiredSecret('Your API key', 'The API key is required');
    }

    /**
     * Send test exception to Honeybadger.
     *
     * @return array
     */
    private function sendTest() : array
    {
        Config::set('honeybadger.api_key', $this->config['api_key']);

        try {
            $result = $this->installer->sendTestException();
        } catch (ServiceException $e) {
            $result = [];
        }

        $this->tasks->addTask(
            'Send test exception to Honeybadger',
            function () use ($result) {
                return ! empty($result);
            }
        );

        return $result;
    }

    /**
     * Write configuration values to the env files.
     *
     * @return void
     */
    private function writeEnv() : void
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
    }

    /**
     * Publish the config file for Lumen or Laravel.
     *
     * @return bool
     */
    public function publishConfig() : bool
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
    private function outputSuccessMessage(string $noticeId) : void
    {
        $this->line(SuccessMessage::make($noticeId));
    }
}
