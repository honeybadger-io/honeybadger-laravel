<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Concerns\HandlesEvents;
use Honeybadger\HoneybadgerLaravel\HoneybadgerLaravel;

class Breadcrumb
{
    use HandlesEvents;

    /**
     * @var string
     */
    public $handles;

    public function handle($event)
    {
        // Doing this check again in case config was changed at runtime (also useful for tests)
        if (
            config('honeybadger.breadcrumbs.enabled', true) === false
            || ! in_array(static::class, config('honeybadger.breadcrumbs.automatic', HoneybadgerLaravel::DEFAULT_BREADCRUMBS))
        ) {
            return;
        }

        try {
            $this->handleEvent($event);
        } catch (\Throwable $e) {
            // Do nothing; we shouldn't crash the user's app for this.
        }
    }
}
