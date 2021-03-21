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
    const VERSION = '3.8.0';

    // Don't forget to sync changes to this with the config file defaults
    const DEFAULT_BREADCRUMBS = [
        Breadcrumbs\DatabaseQueryExecuted::class,
        Breadcrumbs\DatabaseTransactionStarted::class,
        Breadcrumbs\DatabaseTransactionCommitted::class,
        Breadcrumbs\DatabaseTransactionRolledBack::class,
        Breadcrumbs\CacheHit::class,
        Breadcrumbs\CacheMiss::class,
        Breadcrumbs\JobQueued::class,
        Breadcrumbs\MailSending::class,
        Breadcrumbs\MailSent::class,
        Breadcrumbs\MessageLogged::class,
        Breadcrumbs\NotificationSending::class,
        Breadcrumbs\NotificationSent::class,
        Breadcrumbs\NotificationFailed::class,
        Breadcrumbs\RedisCommandExecuted::class,
        Breadcrumbs\RouteMatched::class,
        Breadcrumbs\ViewRendered::class,
    ];

    public static function make(array $config): Reporter
    {
        return static::new(array_merge([
            'notifier' => [
                'name' => 'honeybadger-laravel',
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
        if (app()->bound('session')) {
            // Lumen doesn't come with sessions.
            session()->now('honeybadger_last_error', $result['id'] ?? null);
        }

        return $result;
    }
}
