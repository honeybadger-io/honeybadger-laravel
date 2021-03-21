<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Database\Events\TransactionCommitted;

class DatabaseTransactionCommitted extends Breadcrumb
{
    public $handles = TransactionCommitted::class;

    public function handleEvent(TransactionCommitted $event)
    {
        Honeybadger::addBreadcrumb('Database transaction committed', [
            'connectionName' => $event->connectionName,
        ], 'query');
    }
}
