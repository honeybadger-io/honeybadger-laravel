<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Notifications\Events\NotificationSent as LaravelNotificationSent;

class NotificationSent extends NotificationBreadcrumb
{
    public $handles = LaravelNotificationSent::class;

    public function handleEvent(LaravelNotificationSent $event)
    {
        $metadata = parent::getMetadata($event);
        Honeybadger::addBreadcrumb('Notification sent', $metadata, 'notification');
    }
}
