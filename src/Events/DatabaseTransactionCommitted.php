<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Database\Events\TransactionCommitted;

class DatabaseTransactionCommitted extends ApplicationEvent
{
    public string $handles = TransactionCommitted::class;

    /**
     * @param TransactionCommitted $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        return new EventPayload(
            'query',
            'db.transaction.committed',
            'Database transaction committed',
            ['connectionName' => $event->connectionName],
        );
    }
}
