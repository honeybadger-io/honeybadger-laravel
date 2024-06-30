<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Database\Events\TransactionRolledBack;

class DatabaseTransactionRolledBack extends ApplicationEvent
{
    public string $handles = TransactionRolledBack::class;

    /**
     * @param TransactionRolledBack $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        return new EventPayload(
            'query',
            'Database transaction rolled back',
            ['connectionName' => $event->connectionName],
        );
    }
}
