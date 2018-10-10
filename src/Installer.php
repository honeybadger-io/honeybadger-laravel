<?php

namespace Honeybadger\HoneybadgerLaravel;

use InvalidArgumentException;
use sixlive\DotenvEditor\DotenvEditor;
use Illuminate\Support\Facades\Artisan;
use Honeybadger\Contracts\Reporter as Honeybadger;
use Honeybadger\HoneybadgerLaravel\Exceptions\TestException;
use Honeybadger\HoneybadgerLaravel\Contracts\Installer as InstallerContract;

class Installer implements InstallerContract
{
    protected $honeybadger;

    public function __construct(Honeybadger $honeybadger)
    {
        $this->honeybadger = $honeybadger;
    }

    /**
     * Write the configurations to dotenv files.
     *
     * @param  array  $config
     * @param  string  $file
     * @return bool
     */
    public function writeConfig(array $config, string $filePath) : bool
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

    public function sendTestException() : array
    {
        return $this->honeybadger->notify(new TestException);
    }

    public function publishLaravelConfig() : bool
    {
        return Artisan::call('vendor:publish', [
            '--provider' => HoneybadgerServiceProvider::class,
        ]) === 0;
    }

    public function shouldPublishConfig(): bool
    {
        return file_exists(base_path('config/honeybadger.php'));
    }

    public function publishLumenConfig(): bool
    {
        if (! is_dir(base_path('config'))) {
            mkdir(base_path('config'));
        }

        return copy(
            __DIR__.'/../../config/honeybadger.php',
            base_path('config/honeybadger.php')
        );
    }
}
