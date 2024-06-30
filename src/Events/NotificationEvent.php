<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;

abstract class NotificationEvent extends ApplicationEvent
{
    protected function getMetadata(NotificationSent|NotificationSending|NotificationFailed $event): array
    {
        return [
            'notification' => get_class($event->notification),
            'channel' => $event->channel,
            'queue' => $event->queue,
            'notifiable' => get_class($event->notifiable),
        ];
    }
}
