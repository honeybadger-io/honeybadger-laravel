<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Honeybadger\HoneybadgerLaravel\Concerns\HandlesEvents;
use Honeybadger\HoneybadgerLaravel\HoneybadgerLaravel;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Support\Facades\Log;

abstract class ApplicationEvent
{
    use HandlesEvents;

    public string $handles;

    abstract public function getEventPayload($event): EventPayload;

    public function handle($event): void
    {
        // Doing this check again in case config was changed at runtime (also useful for tests)
        $breadcrumbEnabled = $this->isBreadcrumbEnabled();
        $eventEnabled = $this->isEventEnabled();

        try {
            if (!($breadcrumbEnabled || $eventEnabled)) {
                return;
            }

            $payload = $this->getEventPayload($event);

            if ($breadcrumbEnabled) {
                Honeybadger::addBreadcrumb($payload->message, $payload->metadata, $payload->category);
            }

            if ($eventEnabled) {
                $this->setRequestId($payload);
                Honeybadger::event($payload->type, $payload->metadata);
            }
        } catch (\Throwable $e) {
            // Do nothing; we shouldn't crash the user's app for this.
        }
    }

    /**
     * Calculate the duration in milliseconds.
     * Note: As of this writing, Honeybadger Insights displays duration in microseconds.
     *
     * @param float $startTime
     * @return string|null Duration in milliseconds i.e. 5ms
     */
    protected function getDurationInMs(float $startTime): ?string
    {
        if (!isset($startTime)) {
            return null;
        }

        $duration = microtime(true) - $startTime;
        return number_format($duration * 1000, 3) . 'ms';
    }

    private function isBreadcrumbEnabled(): bool {
        if (!config('honeybadger.breadcrumbs.enabled', true)) {
            return false;
        }

        $eventsEnabled = config('honeybadger.breadcrumbs.automatic', HoneybadgerLaravel::DEFAULT_BREADCRUMB_EVENTS);
        if (in_array(static::class, $eventsEnabled)) {
            return true;
        }

        // also check if the deprecated class name is in the list
        $deprecatedClassName = str_replace('HoneybadgerLaravel\Events', 'HoneybadgerLaravel\Breadcrumbs', static::class);

        return in_array($deprecatedClassName, $eventsEnabled);
    }

    private function isEventEnabled(): bool {
        if (!config('honeybadger.events.enabled', false)) {
            return false;
        }

        return in_array(static::class, config('honeybadger.events.automatic', HoneybadgerLaravel::DEFAULT_EVENTS));
    }

    private function setRequestId($payload): void {
        $logContext = Log::sharedContext();
        if (isset($logContext['requestId']) && !isset($payload->metadata['requestId'])) {
            $payload->metadata['requestId'] = $logContext['requestId'];
        }
    }
}
