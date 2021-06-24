<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Cache\Events\CacheHit as LaravelCacheHit;

class CacheHit extends Breadcrumb
{
    public $handles = LaravelCacheHit::class;

    public function handleEvent(LaravelCacheHit $event)
    {
        Honeybadger::addBreadcrumb('Cache hit', ['key' => $event->key], 'query');
    }
}
