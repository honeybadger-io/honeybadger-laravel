<?php


namespace Honeybadger\HoneybadgerLaravel\Concerns;

use Illuminate\Events\Dispatcher;

trait HandlesEvents
{
    public function register()
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = app('events');
        $dispatcher->listen($this->handles, [$this, 'handleEvent']);
    }
}