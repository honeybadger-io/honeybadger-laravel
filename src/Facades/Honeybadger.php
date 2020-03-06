<?php

namespace Honeybadger\HoneybadgerLaravel\Facades;

use Illuminate\Support\Facades\Facade;

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
