<?php

namespace Honeybadger\HoneybadgerLaravel\Concerns;

trait RequiredInput
{
    /**
     * Prompts for a required secret until its given.
     *
     * @param  string  $question
     * @param  string  $failedMessage
     * @return string
     */
    public function requiredSecret($question, $failedMessage)
    {
        $input = $this->secret($question);

        if (is_null($input)) {
            $this->error($failedMessage);

            return $this->requiredSecret($question, $failedMessage);
        }

        return $input;
    }
}
