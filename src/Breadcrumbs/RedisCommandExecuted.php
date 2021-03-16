<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Concerns\HandlesEvents;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Redis\Events\CommandExecuted;

/**
 * Note that Laravel only dispatches Redis events if the user calls Redis::enableEvents() first.
 */
class RedisCommandExecuted
{
    use HandlesEvents;

    public $handles = CommandExecuted::class;

    public function handleEvent(CommandExecuted $event)
    {
        $metadata = [
            'connectionName' => $event->connectionName,
            'command' => $this->formatCommand($event->command, $event->parameters),
            'duration' => number_format($event->time, 2, '.', ''),
        ];

        Honeybadger::addBreadcrumb('Redis command executed', $metadata, 'query');
    }

    private function formatCommand(string $command, array $parameters)
    {
        $parameters = collect($parameters)->map(function ($parameter) {
            if (is_array($parameter)) {
                return collect($parameter)->map(function ($value, $key) {
                    if (is_array($value)) {
                        return json_encode($value);
                    }

                    return is_int($key) ? $value : "{$key} {$value}";
                })->implode(' ');
            }

            return $parameter;
        })->implode(' ');

        return "{$command} {$parameters}";
    }
}
