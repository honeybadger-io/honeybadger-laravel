<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;

class MailSent extends MailEvent
{
    public string $handles = MessageSent::class;

    /**
     * @param MessageSending $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = parent::getMetadata($event);

        return new EventPayload(
            'mail',
            'mail.sent',
            'Mail sent',
            $metadata,
        );
    }
}
