<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Exception;
use Honeybadger\Honeybadger;
use Illuminate\Console\Command;

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
    public function handle(Honeybadger $honeybadger)
    {
        try {
            $honeybadger->checkin($this->argument('id'));
            $this->info(sprintf('Checkin %s was sent to Honeybadger', $this->argument('id')));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
