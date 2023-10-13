<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Exception;
use Honeybadger\Contracts\Reporter;
use Illuminate\Console\Command;

class HoneybadgerCheckinCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'honeybadger:checkin {idOrName}';

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
            $honeybadger->checkin($this->checkinIdOrName());
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
    private function checkinIdOrName(): string
    {
        return is_array($this->argument('idOrName'))
            ? $this->argument('idOrName')[0]
            : $this->argument('idOrName');
    }
}
