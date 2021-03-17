<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Database\Events\TransactionRolledBack;

class DatabaseTransactionRolledBack extends Breadcrumb
{
    public $handles = TransactionRolledBack::class;

    public function handleEvent(TransactionRolledBack $event)
    {
        Honeybadger::addBreadcrumb('Database transaction rolled back', [
            'connectionName' => $event->connectionName,
        ], 'query');
    }
}
