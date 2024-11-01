<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Exception;
use Honeybadger\Contracts\Reporter;
use Honeybadger\Honeybadger;
use Honeybadger\HoneybadgerLaravel\Exceptions\TestException;
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

    public function handle()
    {
        /** @var Reporter $honeybadger */
        $honeybadger = app('honeybadger.loud');

        try {
            if (! config('honeybadger.report_data')) {
                $this->info("You have `report_data` set to false in your config. Errors won't be reported in this environment.");
                $this->info("We've switched it to true for this test, but you should check that it's enabled for your production environments.");
            }
            $result = $honeybadger->notify(new TestException);
            $id = $result['id'] ?? null;
            if (is_null($id)) {
                throw new Exception('There was an error sending the exception to Honeybadger');
            }

            $appEndpoint = config('honeybadger.app_endpoint') ?? Honeybadger::APP_URL;
            if (!str_ends_with($appEndpoint, '/')) {
                $appEndpoint .= '/';
            }

            $noticeUrl = $appEndpoint . "notice/$id";
            $this->info("Successfully sent a test exception to Honeybadger: $noticeUrl");
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
