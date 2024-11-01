<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\Contracts\Reporter;
use Honeybadger\Exceptions\ServiceException;
use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\Exceptions\TestException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class HoneybadgerLaravel extends Honeybadger
{
    const VERSION = '4.2.2';

    const DEFAULT_EVENTS = [
        Events\DatabaseQueryExecuted::class,
        Events\DatabaseTransactionStarted::class,
        Events\DatabaseTransactionCommitted::class,
        Events\DatabaseTransactionRolledBack::class,
        Events\CacheHit::class,
        Events\CacheMiss::class,
        Events\JobQueued::class,
        Events\JobProcessed::class,
        Events\MailSending::class,
        Events\MailSent::class,
        Events\NotificationSending::class,
        Events\NotificationSent::class,
        Events\NotificationFailed::class,
        Events\RedisCommandExecuted::class,
        Events\ResponseReceived::class,
        Events\RouteMatched::class,
        Events\RequestHandled::class,
        Events\ViewRendered::class,
    ];

    const DEFAULT_BREADCRUMB_EVENTS = [
        ...self::DEFAULT_EVENTS,
        Events\MessageLogged::class,
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
                Log::warning($e);
            },
            'events_exception_handler' => function (ServiceException $e) {
                // noop; we don't want to throw exceptions in the event handlers
                // nor do we want to log them, because they will create a lot of noise.
            },
        ], $config));
    }

    public function notify(Throwable $throwable, Request $request = null, array $additionalParams = []): array
    {
        $this->setRouteActionAndUserContext($request ?: request());

        $result = parent::notify($throwable, $request, $additionalParams);

        // Persist the most recent error for the rest of the request, so we can display on error page.
        if (app()->bound('session')) {
            // Lumen doesn't come with sessions.
            session()->now('honeybadger_last_error', $result['id'] ?? null);
        }

        return $result;
    }

    protected function shouldReport(Throwable $throwable): bool
    {
        // Always report if the user is running a test.
        if ($throwable instanceof TestException) {
            return true;
        }

        return parent::shouldReport($throwable);
    }

    protected function setRouteActionAndUserContext(Request $request): void
    {
        // For backwards compatibility, check if context has already been set by the middleware
        if ($this->context->get('user_id') === null
            && $this->context->get('honeybadger_component') === null
            && $this->context->get('honeybadger_action') === null) {
            $contextManager = new ContextManager($this);
            $contextManager->setRouteAction($request);
            $contextManager->setUserContext($request);
        }
    }
}
