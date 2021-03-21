<?php

namespace Honeybadger\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TestUser extends Authenticatable
{
    use Notifiable;
}
