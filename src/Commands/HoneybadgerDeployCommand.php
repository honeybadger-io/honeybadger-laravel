<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class HoneybadgerDeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'honeybadger:deploy {--apiKey=} {--environment=} {--revision=} {--repository=} {--username=}';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deployment to Honeybadger';

    /**
     * @var \GuzzleHttp\Client
     */
    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->client->post(
            'https://api.honeybadger.io/v1/deploys',
            [
                'form_params' => $this->resolveParams(),
            ]
        );

        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200 && $body['status'] === 'OK') {
            throw new \Exceptions("Sending the deployment to Honeybadger faild. Status Code {$response->getStatusCode()}");
        }
    }

    private function resolveParams() : array
    {
        return array_merge(
            $this->resolveConfigValues(),
            $this->resolveOptions()
        );
    }

    private function resolveConfigValues() : array
    {
        $config = Config::get('honeybadger');

        return [
           'api_key'  => $config['api_key'],
           'revision' => $config['version'] ?? $this->gitHash(),
           'environment' => $config['environment_name'],
        ];
    }

    private function resolveOptions() : array
    {
        return array_filter([
            'api_key' => $this->option('apiKey'),
            'environment' => $this->option('environment'),
            'revision' => $this->option('revision'),
            'repository' => $this->option('repository') ?? $this->gitRemote(),
            'local_username' => $this->option('username') ?? get_current_user(),
        ]);
    }

    private function gitHash() : string
    {
        return trim(exec('git log --pretty="%h" -n1 HEAD'));
    }

    private function gitRemote() : string
    {
        return trim(exec('git config --get remote.origin.url'));
    }
}
