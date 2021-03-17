<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Support\Facades\View;

class ViewRendered extends Breadcrumb
{
    public $handles = 'composing:*';

    public function handleEvent(string $eventName, array $data = [])
    {
        if (!empty($data)) {
            /** @var \Illuminate\View\View $view */
            $view = $data[0];
            $metadata = [
                'name' => $view->getName(),
                'path' => $view->getPath(),
            ];
        } else {
            // $eventName is sometimes "composing: errors::500" with no $data
            $name = explode(': ', $eventName)[1];
            $metadata = [
                'name' => $name,
                'path' => View::getFinder()->find($name),
            ];
        }

        Honeybadger::addBreadcrumb('View rendered', $metadata, 'render');
    }
}
