<?php

namespace Honeybadger\Tests\Commands;

use Throwable;
use Honeybadger\Contracts\Reporter;
use Symfony\Component\HttpFoundation\Request;

class ReporterFake implements Reporter
{
    public function notify(Throwable $throwable, Request $request = null) : array
    {
        return [];
    }

    public function customNotification(array $payload): array
    {
        return [];
    }

    public function context($key, $value) : void
    {
    }

    public function checkin(string $key) : void
    {
    }
}
