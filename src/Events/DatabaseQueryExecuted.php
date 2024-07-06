<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;

class DatabaseQueryExecuted extends ApplicationEvent
{
    public string $handles = QueryExecuted::class;

    /**
     * @param QueryExecuted $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = [
            'connectionName' => $event->connectionName,
            'sql' => $this->sanitize($event->sql, $event->connection),
            'duration' => number_format($event->time, 2, '.', '').'ms',
        ];

        return new EventPayload(
            'query',
            'Database query executed',
            $metadata,
        );
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
        if (!in_array($connection->getConfig('driver'), $doubleQuoters)) {
            $sql = preg_replace($doubleQuotedData, '?', $sql);
        }

        return $sql;
    }
}
