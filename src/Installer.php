<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer as InstallerContract;
use Honeybadger\HoneybadgerLaravel\Exceptions\TestException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use sixlive\DotenvEditor\DotenvEditor;

class Installer implements InstallerContract
{
    /**
     * {@inheritdoc}
     */
    public function writeConfig(array $config, string $filePath): bool
    {
        try {
            $env = new DotenvEditor;
            $env->load($filePath);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        collect($config)->each(function ($value, $key) use ($env) {
            $env->set($key, $value);
        });

        return $env->save();
    }

    /**
     * {@inheritdoc}
     */
    public function sendTestException(): array
    {
        return App::makeWith(
            Reporter::class,
            ['config' => Config::get('honeybadger')]
        )->notify(new TestException);
    }

    /**
     * {@inheritdoc}
     */
    public function publishLaravelConfig(): bool
    {
        return Artisan::call('vendor:publish', [
            '--provider' => HoneybadgerServiceProvider::class,
        ]) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldPublishConfig(): bool
    {
        return ! file_exists(base_path('config/honeybadger.php'));
    }

    /**
     * {@inheritdoc}
     */
    public function publishLumenConfig(string $stubPath = null): bool
    {
        if (! is_dir(base_path('config'))) {
            mkdir(base_path('config'));
        }

        return copy(
            $stubPath ?? __DIR__.'/../config/honeybadger.php',
            base_path('config/honeybadger.php')
        );
    }
}
