<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Database\Events\QueryExecuted;

class DatabaseQueryExecuted extends Breadcrumb
{
    public $handles = QueryExecuted::class;

    public function handleEvent(QueryExecuted $event)
    {
        $metadata = [
            'connectionName' => $event->connectionName,
            'sql' => $event->sql,
            'duration' => number_format($event->time, 2, '.', '').'ms',
        ];

        Honeybadger::addBreadcrumb('Database query executed', $metadata, 'query');
    }
}
