<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Notifications\Events\NotificationFailed as LaravelNotificationFailed;

class NotificationFailed extends NotificationBreadcrumb
{
    public $handles = LaravelNotificationFailed::class;

    public function handleEvent(LaravelNotificationFailed $event)
    {
        $metadata = parent::getMetadata($event);
        Honeybadger::addBreadcrumb('Sending notification', $metadata, 'notification');
    }
}
