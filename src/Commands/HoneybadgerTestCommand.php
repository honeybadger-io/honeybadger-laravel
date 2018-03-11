<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Exception;
use Honeybadger\Honeybadger;
use Illuminate\Console\Command;

class HoneybadgerTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'honeybadger:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests notifications to Honeybadger';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Honeybadger $honeybadger)
    {
        try {
            $honeybadger->notify(new Exception('This is an example exception for Honeybadger'));
            $this->line('A test exception was sent to Honeybadger');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
