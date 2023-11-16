<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Exception;
use Honeybadger\Contracts\Reporter;
use Illuminate\Console\Command;

class HoneybadgerCheckInCommand extends Command
{
    /**
     * The name and signature of the console command.
     * "id" can be the check-in ID or the check-in name.
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
            $idOrName = $this->checkInIdOrName();
            $honeybadger->checkin($idOrName);
            $this->info(sprintf('Check-in %s was sent to Honeybadger', $idOrName));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the API key from input.
     *
     * @return string
     */
    private function checkInIdOrName(): string
    {
        return is_array($this->argument('id'))
            ? $this->argument('id')[0]
            : $this->argument('id');
    }
}
