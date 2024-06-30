<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

class EventPayload {
    /**
     * 'event_type' in case of Honeybadger Insights
     * 'category' in case of Breadcrumbs
     *
     * @var string
     */
    public string $type;

    /**
     * @var string The name of the event
     */
    public string $message;

    /**
     * @var array The metadata of the event
     */
    public array $metadata;

    /**
     * @param string $type
     * @param string $message
     * @param array $metadata
     */
    public function __construct(string $type, string $message, array $metadata)
    {
        $this->type = $type;
        $this->message = $message;
        $this->metadata = $metadata;
    }
}
