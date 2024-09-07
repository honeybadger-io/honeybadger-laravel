<?php

namespace Honeybadger\HoneybadgerLaravel\Concerns;

trait MeasuresDuration
{
    /**
     * @var array{string, float}
     */
    private array $durations = [];

    /**
     * Helper to measure the duration between blocks of code that span multiple files.
     * If the name already exists in the durations array, it will return the duration in milliseconds.
     * If it does not exist, a timer will be started for this name and null will be returned.
     */
    public function time(string $name): ?float {
        if (array_key_exists($name, $this->durations)) {
            $start = $this->durations[$name];
            $end = microtime(true);
            $duration = floor(($end - $start) * 1000);
            unset($this->durations[$name]);
            return $duration;
        } else {
            $this->durations[$name] = microtime(true);
            return null;
        }
    }

}
