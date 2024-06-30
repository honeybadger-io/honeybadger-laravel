<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Cache\Events\CacheHit as LaravelCacheHit;

class CacheHit extends ApplicationEvent
{
    public string $handles = LaravelCacheHit::class;

    /**
     * @param LaravelCacheHit $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        return new EventPayload(
            'query',
            'Cache hit',
            ['key' => $event->key],
        );
    }
}
