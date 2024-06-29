<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

use Honeybadger\HoneybadgerLaravel\Facades\Honeybadger;
use Illuminate\Mail\Events\MessageSending;

class MailSending extends MailBreadcrumb
{
    public string $handles = MessageSending::class;

    /**
     * @param MessageSending $event
     * @return EventPayload
     */
    public function getEventPayload($event): EventPayload
    {
        $metadata = parent::getMetadata($event);

        return new EventPayload(
            'mail',
            'Sending mail',
            $metadata,
        );
    }
}
