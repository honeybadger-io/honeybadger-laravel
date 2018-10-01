<?php

namespace Honeybadger\Tests\Commands;

use Honeybadger\Honeybadger;
use PHPUnit\Framework\TestCase;
use Honeybadger\HoneybadgerLaravel\Installer;

class InstallerTest extends TestCase
{
    /** @test */
    public function configuration_can_be_wrtten()
    {
        $honeybadger = new ReporterFake;

        unlink(__DIR__.'/tmp/.env');
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
    }
}
