<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Concerns\HandlesEvents;
use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;

class ViewRendered
{
    use HandlesEvents;

    public $handles = 'composing:*';

    public function handleEvent(string $eventName, array $data)
    {
        /** @var \Illuminate\View\View $view */
        $view = $data[0];
        $metadata = [
            'name' => $view->getName(),
            'path' => $view->getPath(),
        ];

        Honeybadger::addBreadcrumb('View rendered', $metadata, 'render');
    }
}
