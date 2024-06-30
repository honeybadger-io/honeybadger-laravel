<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Notifications\Events\NotificationFailed as LaravelNotificationFailed;

class NotificationFailed extends NotificationEvent
{
    public string $handles = LaravelNotificationFailed::class;

    /**
     * @param LaravelNotificationFailed $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = parent::getMetadata($event);

        return new EventPayload(
            'notification',
            'Notification failed',
            $metadata,
        );
    }
}
