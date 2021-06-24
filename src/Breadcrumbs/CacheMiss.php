<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Cache\Events\CacheMissed;

class CacheMiss extends Breadcrumb
{
    public $handles = CacheMissed::class;

    public function handleEvent(CacheMissed $event)
    {
        Honeybadger::addBreadcrumb('Cache miss', ['key' => $event->key], 'query');
    }
}
