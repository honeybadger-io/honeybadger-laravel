<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Support\Facades\View;

class ViewRendered extends ApplicationEvent
{
    public string $handles = 'composing:*';

    /**
     * @param string $event
     * @param array $data
     * @return EventPayload
     */
    public function getEventPayload($event, array $data = []): EventPayload
    {
        if (! empty($data)) {
            /** @var \Illuminate\View\View $view */
            $view = $data[0];
            $metadata = [
                'name' => $view->getName(),
                'path' => $view->getPath(),
            ];
        } else {
            // $eventName is sometimes "composing: errors::500" with no $data
            $name = explode(': ', $event)[1];
            $metadata = [
                'name' => $name,
                'path' => View::getFinder()->find($name),
            ];
        }

        return new EventPayload(
            'render',
            'view.rendered',
            'View rendered',
            $metadata,
        );
    }
}
