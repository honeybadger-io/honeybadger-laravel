<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Notifications\Events\NotificationSending as LaravelNotificationSending;

class NotificationSending extends NotificationBreadcrumb
{
    public $handles = LaravelNotificationSending::class;

    public function handleEvent(LaravelNotificationSending $event)
    {
        $metadata = parent::getMetadata($event);
        Honeybadger::addBreadcrumb('Sending notification', $metadata, 'notification');
    }
}
