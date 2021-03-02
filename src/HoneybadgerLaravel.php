<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Contracts\Reporter;
use Honeybadger\Exceptions\ServiceException;
use Honeybadger\Honeybadger;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class HoneybadgerLaravel extends Honeybadger
{
    const VERSION = '3.6.0';

    public static function make(array $config): Reporter
    {
        return static::new(array_merge([
            'notifier' => [
                'name' => 'Honeybadger Laravel',
                'url' => 'https://github.com/honeybadger-io/honeybadger-laravel',
                'version' => self::VERSION.'/'.Honeybadger::VERSION,
            ],
            'service_exception_handler' => function (ServiceException $e) {
                Log::error($e);
            },
        ], $config));
    }

    public function notify(Throwable $throwable, Request $request = null, array $additionalParams = []): array
    {
        $result = parent::notify($throwable, $request, $additionalParams);
        // Persist the most recent error for the rest of the request, so we can display on error page.
        session()->now('honeybadger_last_error', $result['id'] ?? null);

        return $result;
    }
}
