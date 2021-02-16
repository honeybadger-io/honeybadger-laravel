<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Contracts\Reporter;
use Honeybadger\Exceptions\ServiceException;
use Honeybadger\Honeybadger;
use Illuminate\Support\Facades\Log;

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
