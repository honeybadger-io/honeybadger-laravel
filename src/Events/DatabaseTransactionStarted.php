<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Database\Events\TransactionBeginning;

class DatabaseTransactionStarted extends ApplicationEvent
{
    public string $handles = TransactionBeginning::class;

    /**
     * @param TransactionBeginning $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        return new EventPayload(
            'query',
            'Database transaction started',
            ['connectionName' => $event->connectionName],
        );
    }
}
