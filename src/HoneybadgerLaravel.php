<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Honeybadger;
use Honeybadger\Contracts\Reporter;
use Illuminate\Support\Facades\Log;
use Honeybadger\Exceptions\ServiceException;

class HoneybadgerLaravel
{
    const VERSION = '3.5.0';

    /**
     * Honeybadger factory.
     *
     * @param array $config
     *
     * @return \Honeybadger\Contracts\Reporter
     */
    public function make(array $config): Reporter
    {
        return Honeybadger::new(array_merge([
            'notifier' => [
                'name' => 'Honeybadger Laravel',
                'url' => 'https://github.com/honeybadger-io/honeybadger-laravel',
                'version' => self::VERSION,
            ],
            'service_exception_handler' => function (ServiceException $e) {
                Log::error($e);
            },
        ], $config));
    }
}
