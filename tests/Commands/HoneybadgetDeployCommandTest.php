<?php

namespace Honeybadger\Tests\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Honeybadger\Tests\TestCase;
use Honeybadger\HoneybadgerLaravel\Commands\HoneybadgerDeployCommand;

class HoneybadgerDeployCommandTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        $client = new class extends Client {
            protected $response = null;

            public function setResponse($response)
            {
                $this->response = $response;
            }

            public function post($url, $options = [])
            {
                $this->url = $url;
                $this->options = $options;

                return $this->response ?? new Response(200, [], json_encode(['status' => 'OK']));
            }
        };

        $this->client = $client;

        $this->app->when(HoneybadgerDeployCommand::class)
            ->needs(Client::class)
            ->give(function () use ($client) {
                return $client;
            });
    }

    /** @test */
    public function default_params_resolve()
    {
        $this->app['config']->set('honeybadger', [
            'api_key' => 'secret1234',
            'version' => '1.1.0',
            'environment_name' => 'production',
        ]);

        $this->artisan('honeybadger:deploy');
        $this->assertEquals([
            'form_params' => [
                'api_key' => 'secret1234',
                'environment' => 'production',
                'revision' => '1.1.0',
                'repository' => trim(exec('git config --get remote.origin.url')),
                'local_username' => get_current_user(),
            ],
        ], $this->client->options);
    }

    /** @test */
    public function revision_falls_back_to_git_hash()
    {
        $this->app['config']->set('honeybadger', array_merge(
            $this->app['config']->get('honeybadger'),
            ['version' => null]
        ));

        $this->artisan('honeybadger:deploy');

        $this->assertEquals(
            $this->client->options['form_params']['revision'],
            trim(exec('git log --pretty="%h" -n1 HEAD'))
        );
    }

    /** @test */
    public function params_from_options_override_defaults()
    {
        $this->app['config']->set('honeybadger', [
            'api_key' => 'secret1234',
            'version' => '1.1.0',
            'environment_name' => 'production',
        ]);

        $this->artisan('honeybadger:deploy', [
            '--apiKey' => 'supersecret',
            '--environment' => 'staging',
            '--revision' => '2.0',
            '--repository' => 'https://github.com/honeybadger-io/honeybadger-laravel',
            '--username' => 'systemuser',
        ]);

        $this->assertEquals([
            'form_params' => [
                'api_key' => 'supersecret',
                'environment' => 'staging',
                'revision' => '2.0',
                'repository' => 'https://github.com/honeybadger-io/honeybadger-laravel',
                'local_username' => 'systemuser',
            ],
        ], $this->client->options);
    }

    /** @test */
    public function invalid_status_codes_trigger_an_exception()
    {
        $this->client->setResponse(new Response(500));

        try {
            $this->artisan('honeybadger:deploy');
        } catch (\Exception $e) {
            $this->assertRegexp('/500/', $e->getMessage());
        }
    }

    /** @test */
    public function invalid_response_trigger_an_exception()
    {
        $this->client->setResponse(new Response(200, [], json_encode(['status' => 'BAD'])));

        try {
            $this->artisan('honeybadger:deploy');
        } catch (\Exception $e) {
            $this->assertRegexp('/{"status":"BAD"}/', $e->getMessage());
        }
    }
}
