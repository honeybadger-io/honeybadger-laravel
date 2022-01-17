<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Mail\Events\MessageSending;

class MailSending extends MailBreadcrumb
{
    public $handles = MessageSending::class;

    public function handleEvent(MessageSending $event)
    {
        $metadata = parent::getMetadata($event);
        Honeybadger::addBreadcrumb('Sending mail', $metadata, 'mail');
    }
}
