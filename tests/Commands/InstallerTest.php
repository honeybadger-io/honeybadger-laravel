<?php

namespace Honeybadger\Tests\Commands;

use Honeybadger\Tests\TestCase;
use Honeybadger\Contracts\Reporter;
use Illuminate\Support\Facades\Artisan;
use Honeybadger\HoneybadgerLaravel\Installer;
use Honeybadger\HoneybadgerLaravel\Exceptions\TestException;
use Honeybadger\HoneybadgerLaravel\HoneybadgerServiceProvider;

class InstallerTest extends TestCase
{
    /** @test */
    public function gracefully_handles_env_file_not_existing()
    {
        $honeybadger = $this->createMock(Reporter::class);

        $installer = new Installer($honeybadger);

        $result = $installer->writeConfig(
            ['API_KEY' => 'secret', 'APP_DEBUG' => 'true'],
            __DIR__.'/tmp/.env'
        );

        $this->assertFalse($result);
    }

    /** @test */
    public function environment_configuration_can_be_wrtten()
    {
        $honeybadger = $this->createMock(Reporter::class);

        @unlink(__DIR__.'/tmp/.env');
        touch(__DIR__.'/tmp/.env');

        $installer = new Installer($honeybadger);

        $installer->writeConfig(
            ['API_KEY' => 'secret', 'APP_DEBUG' => 'true'],
            __DIR__.'/tmp/.env'
        );

        $this->assertEquals(
            "API_KEY=secret\nAPP_DEBUG=true",
            file_get_contents(__DIR__.'/tmp/.env')
        );

        @unlink(__DIR__.'/tmp/.env');
    }

    /** @test */
    public function a_test_exception_is_sent()
    {
        $honeybadger = $this->createMock(Reporter::class);

        $honeybadger->expects($this->once())
            ->method('notify')
            ->with($this->isInstanceOf(TestException::class));

        $installer = new Installer($honeybadger);

        $installer->sendTestException();
    }

    /** @test */
    public function publishes_config_for_laravel()
    {
        $honeybadger = $this->createMock(Reporter::class);

        Artisan::shouldReceive('call')
            ->once()
            ->with('vendor:publish', [
                '--provider' => HoneybadgerServiceProvider::class,
            ])->andReturn(0);

        $installer = new Installer($honeybadger);

        $this->assertTrue($installer->publishLaravelConfig());
    }

    /** @test */
    public function publish_should_be_configed()
    {
        $honeybadger = $this->createMock(Reporter::class);

        $installer = new Installer($honeybadger);

        @mkdir(__DIR__.'/tmp/config');

        $this->app->setBasePath(__DIR__.'/tmp');

        $this->assertTrue($installer->shouldPublishConfig());

        @rmdir(__DIR__.'/tmp/config');
    }

    /** @test */
    public function publish_should_not_be_configed()
    {
        $honeybadger = $this->createMock(Reporter::class);

        $installer = new Installer($honeybadger);

        @mkdir(__DIR__.'/tmp/config');
        @touch(__DIR__.'/tmp/config/honeybadger.php');

        $this->app->setBasePath(__DIR__.'/tmp');

        $this->assertFalse($installer->shouldPublishConfig());

        @unlink(__DIR__.'/tmp/config/honeybadger.php');
        @rmdir(__DIR__.'/tmp/config');
    }

    /** @test */
    public function publishes_lumen_config()
    {
        $honeybadger = $this->createMock(Reporter::class);

        $installer = new Installer($honeybadger);

        $this->app->setBasePath(__DIR__.'/tmp');

        $stubPath = __DIR__.'/../../config/honeybadger.php';

        $this->assertTrue($installer->publishLumenConfig($stubPath));
        $this->assertTrue(file_exists(__DIR__.'/tmp/config/honeybadger.php'));

        @unlink(__DIR__.'/tmp/config/honeybadger.php');
        @rmdir(__DIR__.'/tmp/config');
    }
}
