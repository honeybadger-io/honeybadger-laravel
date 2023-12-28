<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Exception;
use Honeybadger\Contracts\Reporter;
use Illuminate\Console\Command;

class HoneybadgerCheckInCommand extends Command
{
    /**
     * The name and signature of the console command.
     * "id" can be the check-in ID or the check-in slug.
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
            $idOrSlug = $this->checkInIdOrSlug();
            $honeybadger->checkin($idOrSlug);
            $this->info(sprintf('Check-in %s was sent to Honeybadger', $idOrSlug));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function checkInIdOrSlug(): string
    {
        return is_array($this->argument('id'))
            ? $this->argument('id')[0]
            : $this->argument('id');
    }
}
