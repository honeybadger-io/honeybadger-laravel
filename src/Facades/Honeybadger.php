<?php

namespace Honeybadger\HoneybadgerLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void event(string|array $eventTypeOrPayload, array $payload = null)
 * @method static void checkin(string $key)
 * @method static array rawNotification(callable $callable)
 * @method static array customNotification(array $payload)
 * @method static array notify(\Throwable $throwable, \Symfony\Component\HttpFoundation\Request $request = null, array $additionalParams = [])
 * @method static \Honeybadger\Contracts\Reporter context(int|string|array $key, $value = null)
 * @method static \Honeybadger\Contracts\Reporter addBreadcrumb(string $message, array $metadata = [], string $category = 'custom')
 * @method static \Honeybadger\Contracts\Reporter resetContext()
 * @method static \Honeybadger\Contracts\Reporter clear()
 */
class Honeybadger extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'honeybadger';
    }
}
