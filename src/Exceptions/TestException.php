<?php

namespace Honeybadger\HoneybadgerLaravel\Exceptions;

class TestException extends \Exception
{
    public function __construct()
    {
        parent::__construct('This is an example exception for Honeybadger');
    }
}
