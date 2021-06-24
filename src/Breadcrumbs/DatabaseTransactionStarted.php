<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Database\Events\TransactionBeginning;

class DatabaseTransactionStarted extends Breadcrumb
{
    public $handles = TransactionBeginning::class;

    public function handleEvent(TransactionBeginning $event)
    {
        Honeybadger::addBreadcrumb('Database transaction started', [
            'connectionName' => $event->connectionName,
        ], 'query');
    }
}
