<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;

class DatabaseQueryExecuted extends Breadcrumb
{
    public $handles = QueryExecuted::class;

    public function handleEvent(QueryExecuted $event)
    {
        $metadata = [
            'connectionName' => $event->connectionName,
            'sql' => $this->sanitize($event->sql, $event->connection),
            'duration' => number_format($event->time, 2, '.', '').'ms',
        ];

        Honeybadger::addBreadcrumb('Database query executed', $metadata, 'query');
    }

    /**
     * Even though Laravel gives us the sanitized query, let's err on the side of caution by removing any quoted data.
     */
    public function sanitize(string $sql, Connection $connection): string
    {
        $escapedQuotes = '#/(\\"|\\\')/#';
        $numericData = '#\b\d+\b#';
        $singleQuotedData = "#'(?:[^']|'')*'#";
        $newlines = '#\n#';
        $doubleQuotedData = '#"(?:[^"]|"")*"#';

        $sql = preg_replace($escapedQuotes, '', $sql);
        $sql = preg_replace([$numericData, $singleQuotedData, $newlines], '?', $sql);

        $doubleQuoters = ['pgsql', 'sqlite', 'postgis'];
        if (in_array($connection->getConfig('driver'), $doubleQuoters)) {
            $sql = preg_replace($doubleQuotedData, '?', $sql);
        }

        return $sql;
    }
}
