<?php

namespace Honeybadger\HoneybadgerLaravel\Commands;

use Exception;
use Honeybadger\Contracts\Reporter;
use Honeybadger\HoneybadgerLaravel\Exceptions\TestException;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

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
    public function handle(Reporter $honeybadger)
    {
        try {
            $result = $honeybadger->notify(new TestException);
            $this->info('A test exception was sent to Honeybadger');
            if (is_null(Arr::get($result, 'id'))) {
                throw new Exception('There was an error sending the exception to Honeybadger');
            }

            $this->line(sprintf('https://app.honeybadger.io/notice/', Arr::get($result, 'id')));
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
