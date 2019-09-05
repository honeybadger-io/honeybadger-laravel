<?php

namespace Honeybadger\Tests\Fixtures;

class TestInvokableController
{
    public function __invoke()
    {
        return response()->json([]);
    }
}
