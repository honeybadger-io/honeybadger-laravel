<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Mail\Events\MessageSent;

class MailSent extends MailBreadcrumb
{
    public $handles = MessageSent::class;

    public function handleEvent(MessageSent $event)
    {
        $metadata = parent::getMetadata($event);
        Honeybadger::addBreadcrumb('Mail sent', $metadata, 'mail');
    }
}
