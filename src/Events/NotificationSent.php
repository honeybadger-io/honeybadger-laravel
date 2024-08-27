<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Notifications\Events\NotificationSent as LaravelNotificationSent;

class NotificationSent extends NotificationEvent
{
    public string $handles = LaravelNotificationSent::class;

    /**
     * @param LaravelNotificationSent $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = parent::getMetadata($event);

        return new EventPayload(
            'notification',
            'notification.sent',
            'Notification sent',
            $metadata,
        );
    }
}
