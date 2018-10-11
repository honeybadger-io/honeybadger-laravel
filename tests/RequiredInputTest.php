<?php

namespace Honeybadger\Tests;

use PHPUnit\Framework\TestCase;
use Honeybadger\HoneybadgerLaravel\Concerns\RequiredInput;

class RquiredInputTest extends TestCase
{
    /** @test */
    public function answer_is_returned_if_input_is_given()
    {
        $command = new class {
            use RequiredInput;

            public function error($text)
            {
                //
            }

            public function secret($text)
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

            public function secret($question)
            {
                $answer = $this->answers[$this->calls];
                $this->calls++;

                return $answer;
            }

            public function error($message)
            {
                $this->errorMessage = $message;
            }
        };

        $command->requiredSecret('Some question', 'Answer required');
        $this->assertEquals(2, $command->calls);
        $this->assertEquals('Answer required', $command->errorMessage);
    }
}
