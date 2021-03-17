<?php

namespace Honeybadger\HoneybadgerLaravel\Breadcrumbs;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Mail\Events\MessageSending;

class MailSending extends Breadcrumb
{
    public $handles = MessageSending::class;

    public function handleEvent(MessageSending $event)
    {
        $metadata = [
            'queue' => $event->data['queue'] ?? null,
            'replyTo' => $event->message->getReplyTo(),
            'to' => implode(',', array_keys($event->message->getTo())),
            'cc' => implode(',', array_keys($event->message->getCc() ?? [])) ?: null,
            'bcc' => implode(',', array_keys($event->message->getBcc() ?? [])) ?: null,
            'subject' => $event->message->getSubject(),
        ];

        Honeybadger::addBreadcrumb('Sending mail', $metadata, 'mail');
    }
}
