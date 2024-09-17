<?php

namespace Honeybadger\HoneybadgerLaravel\Events;

class EventPayload {
    /**
     * @var string The category of the event, used in notice breadcrumbs
     */
    public string $category;

    /**
     * @var string The type of the event, used in Insights
     */
    public string $type;

    /**
     * @var string The readable message of the event
     */
    public string $message;

    /**
     * @var array The metadata of the event
     */
    public array $metadata;

    /**
     * @param string $category
     * @param string $type
     * @param string $message
     * @param array $metadata
     */
    public function __construct(string $category, string $type, string $message, array $metadata)
    {
        $this->category = $category;
        $this->type = $type;
        $this->message = $message;
        $this->metadata = $metadata;
    }
}
