<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Honeybadger\HoneybadgerLaravel\CommandTasks;
use Honeybadger\HoneybadgerLaravel\Concerns\RequiredInput;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Throwable;

class HoneybadgerInstallCommand extends Command
{
    use RequiredInput;

    private const HONEYBADGER_API_URL_EU = "https://eu-api.honeybadger.io/v1";
    private const HONEYBADGER_APP_URL_EU = "https://eu-app.honeybadger.io";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'honeybadger:install {apiKey?} {--eu} {--endpoint=} {--appEndpoint=}';

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
            $testExceptionResult = $this->tasks->getResults()['Send test exception to Honeybadger'];
            $this->outputSuccessMessage(Arr::get($testExceptionResult, 'id', ''));
        } catch (Throwable $e) {
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
        $config = [
            'api_key' => $this->argument('apiKey') ?? $this->promptForApiKey(),
        ];

        $euStack = $this->option('eu');
        if ($euStack) {
            $config['endpoint'] = self::HONEYBADGER_API_URL_EU;
            $config['app_endpoint'] = self::HONEYBADGER_APP_URL_EU;
        }

        $endpoint = $this->option('endpoint');
        if ($endpoint != null) {
            $config['endpoint'] = $endpoint;
        }
        $appEndpoint = $this->option('appEndpoint');
        if ($appEndpoint != null) {
            $config['app_endpoint'] = $appEndpoint;
        }

        return $config;
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
                if (! config('honeybadger.report_data')) {
                    $this->info("You have `report_data` set to false in your config. Errors won't be reported in this environment.");
                    $this->info("We've switched it to true for this test, but you should check that it's enabled for your production environments.");
                }
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
                    [
                        'HONEYBADGER_API_KEY' => $this->config['api_key'],
                        'HONEYBADGER_VERIFY_SSL' => 'true',
                    ],
                    base_path('.env')
                );
            }
        );

        $this->tasks->addTask(
            'Write HONEYBADGER_API_KEY and HONEYBADGER_VERIFY_SSL placeholders to .env.example',
            function () {
                return $this->installer->writeConfig(
                    [
                        'HONEYBADGER_API_KEY' => '',
                        'HONEYBADGER_VERIFY_SSL' => '',
                    ],
                    base_path('.env.example')
                );
            }
        );

        if (isset($this->config['endpoint'])) {
            $this->tasks->addTask(
                'Write HONEYBADGER_ENDPOINT to .env',
                function () {
                    return $this->installer->writeConfig([
                        'HONEYBADGER_ENDPOINT' => $this->config['endpoint'],
                    ], base_path('.env'));
                }
            );

            $this->tasks->addTask(
                'Write HONEYBADGER_ENDPOINT to .env.example',
                function () {
                    return $this->installer->writeConfig([
                        'HONEYBADGER_ENDPOINT' => $this->config['endpoint'],

                    ], base_path('.env.example'));
                }
            );
        }

        if (isset($this->config['app_endpoint'])) {
            $this->tasks->addTask(
                'Write HONEYBADGER_APP_ENDPOINT to .env',
                function () {
                    return $this->installer->writeConfig([
                        'HONEYBADGER_APP_ENDPOINT' => $this->config['app_endpoint'],
                    ], base_path('.env'));
                }
            );

            $this->tasks->addTask(
                'Write HONEYBADGER_APP_ENDPOINT to .env.example',
                function () {
                    return $this->installer->writeConfig([
                        'HONEYBADGER_APP_ENDPOINT' => $this->config['app_endpoint'],
                    ], base_path('.env.example'));
                }
            );
        }
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
