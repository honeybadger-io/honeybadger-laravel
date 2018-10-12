<?php

namespace Honeybadger\HoneybadgerLaravel;

use Monolog\Logger;
use Honeybadger\Contracts\Reporter;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;

class LogHandler extends AbstractProcessingHandler
{
    /**
     * @var \Honeybadger\Contracts\Reporter
     */
    protected $honeybadger;

    /**
     * @param  \Honeybadger\Contracts\Reporter  $honeybadger
     * @param  int  $level
     * @param  bool  $bubble
     */
    public function __construct(Reporter $honeybadger, int $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->honeybadger = $honeybadger;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $this->honeybadger->rawNotification(function ($config) use ($record) {
            return [
                'notifier' => array_merge($config['notifier'], ['name' => 'Honeybadger Log Handler']),
                'error' => [
                    'class' => $record['message'],
                    'message' => $record['formatted'],
                    'tags' => [
                        'log',
                        sprintf('%s.%s', $record['channel'], $record['level_name']),
                    ],
                    'fingerprint' => md5($record['formatted']),
                ],
                'request' => [
                    'context' => [
                        'context' => $record['context'],
                        'level_name' => $record['level_name'],
                        'log_channel' => $record['channel'],
                        'message' => $record['message'],
                    ],
                ],
            ];
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter() : FormatterInterface
    {
        return new LineFormatter('[%datetime%] %channel%.%level_name%: %message%');
    }
}
