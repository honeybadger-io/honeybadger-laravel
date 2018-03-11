<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Honeybadger;

class HoneybadgerLaravel
{
    const VERSION = '1.0.0';

    public function make($config)
    {
        return Honeybadger::new(array_merge([
            'notifier' => [
                'name' => 'Honeybadger Laravel',
                'url' => 'https://github.com/honeybadger-io/honeybadger-laravel',
                'version' => self::VERSION,
            ],
        ], $config));
    }
}
