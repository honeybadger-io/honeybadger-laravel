<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Honeybadger\Honeybadger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Honeybadger\HoneybadgerLaravel\CommandTasks;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer;
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
                $this->publishConfig()
            );
        }

        if ($this->config['send_test']) {
            $this->sendTest();
        }

        $this->tasks->outputResults();

        $this->outputSuccessMessage();
    }

    /**
     * Prompt for input and gather responses.
     *
     * @return array
     */
    private function gatherConfig()
    {
        return [
            'api_key' => $this->argument('apiKey') ?? $this->promptForApiKey(),
            'send_test' => $this->confirm('Would you like to send a test exception now?', true),
        ];
    }

    /**
     * Prompt for the API key.
     *
     * @return string
     */
    private function promptForApiKey()
    {
        return $this->requiredSecret('Your API key', 'The API key is required');
    }

    /**
     * Send test exception to Honeybadger.
     *
     * @return void
     */
    private function sendTest()
    {
        Config::set('honeybadger.api_key', $this->config['api_key']);

        $result = $this->installer->sendTestException();

        $this->tasks->addTask(
            'Send test exception to Honeybadger',
            ! empty($result)
        );
    }

    /**
     * Write configuration values to the env files.
     *
     * @return void
     */
    private function writeEnv()
    {
        $this->tasks->addTask(
            'Write HONEYBADGER_API_KEY to .env',
            $this->installer->writeConfig(
                ['HONEYBADGER_API_KEY' => $this->config['api_key']],
                base_path('.env')
            )
        );

        $this->tasks->addTask(
            'Write HONEYBADGER_API_KEY placeholder to .env.example',
            $this->installer->writeConfig(
                ['HONEYBADGER_API_KEY' => ''],
                base_path('.env.example')
            )
        );
    }

    public function publishConfig()
    {
        if (app('honeybadger.isLumen')) {
            return $this->installer->publishLumenConfig();
        }

        return $this->installer->publishLaravelConfig();
    }

    private function outputSuccessMessage()
    {
        $message = <<<'EOT'
⚡ --- Honeybadger is installed! -----------------------------------------------
Good news: You're one deploy away from seeing all of your exceptions in
Honeybadger. For now, we've generated a test exception for you:

    #{notice_url}

If you ever need help:

    - Check out the documentation: https://docs.honeybadger.io/lib/php/index.html
    - Email the 'badgers: support@honeybadger.io

Most people don't realize that Honeybadger is a small, bootstrapped company. We
really couldn't do this without you. Thank you for allowing us to do what we
love: making developers awesome.

Happy 'badgering!

Sincerely,
Ben, Josh and Starr
https://www.honeybadger.io/about/
⚡ --- End --------------------------------------------------------------------
EOT;
        $this->line($message);
    }
}
