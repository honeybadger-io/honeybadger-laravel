<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Mail\Events\MessageSent;

class MailSent extends Breadcrumb
{
    public $handles = MessageSent::class;

    public function handleEvent(MessageSent $event)
    {
        $metadata = [
            'queue' => $event->data['queue'] ?? null,
            'replyTo' => $event->message->getReplyTo(),
            'to' => implode(',', array_keys($event->message->getTo())),
            'cc' => implode(',', array_keys($event->message->getCc() ?? [])) ?: null,
            'bcc' => implode(',', array_keys($event->message->getBcc() ?? [])) ?: null,
            'subject' => $event->message->getSubject(),
        ];

        Honeybadger::addBreadcrumb('Mail sent', $metadata, 'mail');
    }
}
