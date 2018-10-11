<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Exception;
use Illuminate\Console\Command;
use Honeybadger\Contracts\Reporter;

class HoneybadgerCheckinCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'honeybadger:checkin {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send check-in to Honeybadger';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Reporter $honeybadger)
    {
        try {
            $honeybadger->checkin($this->apiKey());
            $this->info(sprintf('Checkin %s was sent to Honeybadger', $this->argument('id')));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the API key from input.
     *
     * @return string
     */
    private function apiKey() : string
    {
        return is_array($this->argument('id'))
            ? $this->argument('id')[0]
            : $this->argument('id');
    }
}
