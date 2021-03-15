<?php


namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Concerns\HandlesEvents;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Database\Events\QueryExecuted;

class DatabaseQueryExecuted
{
    use HandlesEvents;

    public $handles = QueryExecuted::class;

    public function handleEvent(QueryExecuted $event)
    {
        $metadata = [
            'connectionName' => $event->connectionName,
            'sql' => $event->sql,
            'duration' => number_format($event->time, 2, '.', ''),
        ];

        Honeybadger::addBreadcrumb('Database query executed', $metadata, 'query');
    }
}
