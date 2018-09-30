<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Honeybadger\Honeybadger;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use sixlive\DotenvEditor\DotenvEditor;
use Honeybadger\HoneybadgerLaravel\Concerns\RequiredInput;
use Honeybadger\HoneybadgerLaravel\Installer;

abstract class HoneybadgerInstallCommand extends Command
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
     * Results of each step of the install.
     *
     * @var array
     */
    protected $results = [];

    /**
     * Configuration from gathered input.
     *
     * @var array
     */
    protected $config = [];

    protected $installer;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->config = $this->gatherConfig();

        $this->installer = new Installer;

        $this->writeEnv();

        if ($this->shouldPublishConfig()) {
            $this->task(
                'Publish the config file',
                $this->publishConfig()
            );
        }

        if ($this->config['send_test']) {
            $this->sendTest();
        }

        $this->outputResults();

        $this->outputSuccessMessage();
    }

    /**
     * Publish the configuration file to the framework.
     *
     * @return bool
     */
    abstract public function publishConfig();

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

        $this->task(
            'Send test exception to Honeybadger',
            $this->callSilent('honeybadger:test') === 0
        );
    }

    /**
     * Write configuration values to the env files.
     *
     * @return void
     */
    private function writeEnv()
    {
        $this->task(
            'Write HONEYBADGER_API_KEY to .env',
            $this->installer->writeConfig(
                ['HONEYBADGER_API_KEY' => $this->config['api_key']],
                base_path('.env')
            )
        );

        $this->task(
            'Write HONEYBADGER_API_KEY placeholder to .env.example',
            $this->installer->writeConfig(
                ['HONEYBADGER_API_KEY' => ''],
                base_path('.env.example')
            )
        );
    }

    /**
     * Whether the configuration needs to be published or no.
     *
     * @return bool
     */
    private function shouldPublishConfig()
    {
        return ! file_exists(base_path('config/honeybadger.php'));
    }

    /**
     * Add the results of each installation step.
     *
     * @param  string  $name
     * @param  bool  $result
     * @return void
     */
    public function task($name, $result)
    {
        $this->results[$name] = $result;
    }

    /**
     * Output the results of each step of the installation.
     *
     * @return void
     */
    private function outputResults()
    {
        collect($this->results)->each(function ($result, $description) {
            $this->line(vsprintf('%s: %s', [
                $description,
                $result ? '<fg=green>✔</>' : '<fg=red>✘</>',
            ]));
        });
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
