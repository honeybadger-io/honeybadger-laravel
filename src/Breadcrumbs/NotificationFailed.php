<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Notifications\Events\NotificationFailed as LaravelNotificationFailed;

class NotificationFailed extends Breadcrumb
{
    public $handles = LaravelNotificationFailed::class;

    public function handleEvent(LaravelNotificationFailed $event)
    {
        $metadata = [
            'notification' => get_class($event->notification),
            'channel' => $event->channel,
            'queue' => $event->queue,
            'notifiable' => get_class($event->notifiable),
        ];

        Honeybadger::addBreadcrumb('Sending notification', $metadata, 'notification');
    }
}
