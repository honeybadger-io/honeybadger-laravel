<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Notifications\Events\NotificationSending as LaravelNotificationSending;

class NotificationSending extends Breadcrumb
{
    public $handles = LaravelNotificationSending::class;

    public function handleEvent(LaravelNotificationSending $event)
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
