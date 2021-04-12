<?php

namespace Honeybadger\Tests\Fixtures;

class TestController
{
    public function index()
    {
        return response()->json([]);
    }

    public function recordException()
    {
        app('honeybadger')->notify(new \Exception('Test exception'));

        return response()->json([]);
    }
}
