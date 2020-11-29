<?php

namespace Honeybadger\Tests;

use Honeybadger\HoneybadgerLaravel\Concerns\RequiredInput;
use PHPUnit\Framework\TestCase;

class RequiredInputTest extends TestCase
{
    /** @test */
    public function answer_is_returned_if_input_is_given()
    {
        $command = new class {
            use RequiredInput;

            public function error($text, $verbosity = null)
            {
                //
            }

            public function secret($text, $fallback = true)
            {
                return 'secret answer';
            }
        };

        $answer = $command->requiredSecret('Some question', 'Answer required');

        $this->assertEquals('secret answer', $answer);
    }

    /** @test */
    public function answer_is_required()
    {
        $command = new class {
            use RequiredInput;

            public $calls = 0;
            public $errorMessage = '';
            public $answers = [
                null,
                'secret answer',
            ];

            public function secret($question, $fallback = true)
            {
                $answer = $this->answers[$this->calls];
                $this->calls++;

                return $answer;
            }

            public function error($message, $verbosity = null)
            {
                $this->errorMessage = $message;
            }
        };

        $command->requiredSecret('Some question', 'Answer required');
        $this->assertEquals(2, $command->calls);
        $this->assertEquals('Answer required', $command->errorMessage);
    }
}
