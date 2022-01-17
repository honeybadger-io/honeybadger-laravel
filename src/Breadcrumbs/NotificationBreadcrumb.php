<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;

abstract class NotificationBreadcrumb extends Breadcrumb
{
    /**
     * @param  NotificationSending|NotificationFailed|NotificationSent  $event
     * @return array
     */
    protected function getMetadata($event): array
    {
        return [
            'notification' => get_class($event->notification),
            'channel' => $event->channel,
            'queue' => $event->queue,
            'notifiable' => get_class($event->notifiable),
        ];
    }
}
