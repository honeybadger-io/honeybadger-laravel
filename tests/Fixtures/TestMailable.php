<?php

namespace Honeybadger\Tests\Fixtures;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('HAhaHA')->view('test');
    }
}
