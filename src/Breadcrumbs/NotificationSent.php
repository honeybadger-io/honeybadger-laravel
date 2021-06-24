<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Notifications\Events\NotificationSent as LaravelNotificationSent;

class NotificationSent extends Breadcrumb
{
    public $handles = LaravelNotificationSent::class;

    public function handleEvent(LaravelNotificationSent $event)
    {
        $metadata = [
            'notification' => get_class($event->notification),
            'channel' => $event->channel,
            'queue' => $event->queue,
            'notifiable' => get_class($event->notifiable),
        ];

        Honeybadger::addBreadcrumb('Notification sent', $metadata, 'notification');
    }
}
