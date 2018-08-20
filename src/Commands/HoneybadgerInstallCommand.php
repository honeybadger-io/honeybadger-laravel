<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Honeybadger\Honeybadger;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use sixlive\DotenvEditor\DotenvEditor;

abstract class HoneybadgerInstallCommand extends Command
{
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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->config = $this->gatherConfig();

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
            'api_key' => $this->argument('apiKey') ?? $this->secret('Please enter your API key'),
            'send_test' => $this->confirm('Would you like to send a test exception now?', true),
        ];
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
            $this->writeConfig(['HONEYBADGER_API_KEY' => $this->config['api_key']])
        );

        $this->task(
            'Write HONEYBADGER_API_KEY placeholder to .env.example',
            $this->writeConfig(['HONEYBADGER_API_KEY' => ''], '.env.example')
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
     * Write the configurations to dotenv files.
     *
     * @param  string  $config
     * @param  string  $file
     * @return bool
     */
    private function writeConfig($config, $file = '.env')
    {
        try {
            $env = new DotenvEditor;
            $env->load(base_path($file));
        } catch (InvalidArgumentException $e) {
            return false;
        }

        collect($config)->each(function ($value, $key) use ($env) {
            $env->set($key, $value);
        });

        return $env->save();
    }

    /**
     * Add the results of each installation step.
     *
     * @param  string  $name
     * @param  string  $result
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
}
