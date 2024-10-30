<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use GuzzleHttp\Client;
use Honeybadger\Honeybadger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

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
        $config = Config::get('honeybadger');

        $endpoint = $config['endpoint'] ?? Honeybadger::API_URL;

        $params = $this->resolveParams($config);

        $response = $this->client->post(
            $endpoint . '/deploys',
            [
                'form_params' => $params,
            ]
        );

        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== Response::HTTP_CREATED || $body['status'] !== 'OK') {
            throw new \Exception(vsprintf('Sending the deployment to Honeybadger failed. Status code %s. Response %s.', [
                $response->getStatusCode(),
                (string) $response->getBody(),
            ]));
        }

        $this->info(sprintf('Deployment %s successfully sent', $params['deploy']['revision']));
    }

    private function resolveParams($config): array
    {
        return [
            'api_key' => $this->option('apiKey') ?? $config['api_key'],
            'deploy' => array_merge([
                'revision' => $config['version'] ?? $this->gitHash(),
                'environment' => $config['environment_name'],
            ], $this->resolveOptions()),
        ];
    }

    private function resolveOptions(): array
    {
        return array_filter([
            'environment' => $this->option('environment'),
            'revision' => $this->option('revision'),
            'repository' => $this->option('repository') ?? $this->gitRemote(),
            'local_username' => $this->option('username') ?? get_current_user(),
        ]);
    }

    private function gitHash(): string
    {
        return trim(exec('git log --pretty="%h" -n1 HEAD'));
    }

    private function gitRemote(): string
    {
        return trim(exec('git config --get remote.origin.url'));
    }
}
