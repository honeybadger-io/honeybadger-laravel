<?php

namespace Honeybadger\HoneybadgerLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class Honeybadger extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'honeybadger';
    }
}
