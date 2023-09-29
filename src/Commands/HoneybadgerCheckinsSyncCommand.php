<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Exception;
use Honeybadger\Contracts\CheckinsSync;
use Honeybadger\Contracts\Reporter;
use Illuminate\Console\Command;

class HoneybadgerCheckinsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'honeybadger:checkins-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize checkins to Honeybadger';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CheckinsSync $checkinsManager)
    {
        try {
            $localCheckins = config('honeybadger.checkins', []);
            $result = $checkinsManager->sync($localCheckins);
            $msg = ['Checkins were synchronized with Honeybadger.'];
            foreach ($result as $checkin) {
                $msg[] = sprintf(
                    'Checkin %s was %s',
                    $checkin->name,
                    $checkin->isDeleted() ? 'removed' : 'synchronized'
                );
            }
            $this->info(implode(PHP_EOL, $msg));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
