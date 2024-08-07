<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Honeybadger\HoneybadgerLaravel\Concerns\HandlesEvents;
use Honeybadger\HoneybadgerLaravel\HoneybadgerLaravel;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;

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
            if ($breadcrumbEnabled || $eventEnabled) {
                $payload = $this->getEventPayload($event);
            }

            if ($breadcrumbEnabled) {
                Honeybadger::addBreadcrumb($payload->message, $payload->metadata, $payload->type);
            }

            if ($eventEnabled) {
                $merged = array_merge($payload->metadata, [
                    'event_type' => $payload->type,
                    'message' => $payload->message
                ]);
                Honeybadger::event($merged);
            }
        } catch (\Throwable $e) {
            // Do nothing; we shouldn't crash the user's app for this.
        }
    }

    private function isBreadcrumbEnabled(): bool {
        if (!config('honeybadger.breadcrumbs.enabled', true)) {
            return false;
        }

        $eventsEnabled = config('honeybadger.breadcrumbs.automatic', HoneybadgerLaravel::DEFAULT_EVENTS);
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
}
