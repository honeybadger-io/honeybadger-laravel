<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Cache\Events\CacheMissed;

class CacheMiss extends ApplicationEvent
{
    public string $handles = CacheMissed::class;

    /**
     * @param CacheMissed $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        return new EventPayload(
            'query',
            'Cache miss',
            ['key' => $event->key],
        );
    }
}
