<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Redis\Events\CommandExecuted;

/**
 * Note that Laravel only dispatches Redis events if the user calls Redis::enableEvents() first.
 */
class RedisCommandExecuted extends ApplicationEvent
{
    public string $handles = CommandExecuted::class;

    /**
     * @param CommandExecuted $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = [
            'connectionName' => $event->connectionName,
            'command' => $this->formatCommand($event->command, $event->parameters),
            'duration' => number_format($event->time, 2, '.', '').'ms',
        ];

        return new EventPayload(
            'query',
            'Redis command executed',
            $metadata,
        );
    }

    private function formatCommand(string $command, array $parameters): string
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
