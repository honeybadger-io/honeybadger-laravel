<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Notifications\Events\NotificationSending as LaravelNotificationSending;

class NotificationSending extends NotificationEvent
{
    public string $handles = LaravelNotificationSending::class;

    /**
     * @param LaravelNotificationSending $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = parent::getMetadata($event);

        return new EventPayload(
            'notification',
            'notification.sending',
            'Sending notification',
            $metadata,
        );
    }
}
