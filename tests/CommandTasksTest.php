<?php

namespace Honeybadger\Tests;

use Illuminate\Console\OutputStyle;
use Honeybadger\HoneybadgerLaravel\CommandTasks;

class CommandTasksTest extends TestCase
{
    /** @test */
    public function outputs_successful_tasks()
    {
        $output = $this->createMock(OutputStyle::class);

        $output->expects($this->once())
            ->method('writeLn')
            ->with('Example Task: <fg=green>✔</>');

        $commandTasks = new CommandTasks;
        $commandTasks->setOutput($output);
        $commandTasks->addTask('Example Task', true);

        $commandTasks->outputResults();

        $this->assertEquals([
            'Example Task' => true,
        ], $commandTasks->getResults());
    }

    /** @test */
    public function outputs_unsuccessful_tasks()
    {
        $output = $this->createMock(OutputStyle::class);

        $output->expects($this->once())
            ->method('writeLn')
            ->with('Example Task: <fg=red>✘</>');

        $commandTasks = new CommandTasks;
        $commandTasks->setOutput($output);
        $commandTasks->addTask('Example Task', false);

        $commandTasks->outputResults();

        $this->assertEquals([
            'Example Task' => false,
        ], $commandTasks->getResults());
    }

    /** @test */
    public function outputs_multiple_tasks()
    {
        $output = $this->createMock(OutputStyle::class);

        $output->expects($this->exactly(2))
            ->method('writeLn')
            ->withConsecutive(
                ['Example successful task: <fg=green>✔</>'],
                ['Example failed task: <fg=red>✘</>']
            );

        $commandTasks = new CommandTasks;
        $commandTasks->setOutput($output);
        $commandTasks->addTask('Example successful task', true);
        $commandTasks->addTask('Example failed task', false);

        $commandTasks->outputResults();

        $this->assertEquals([
            'Example successful task' => true,
            'Example failed task' => false,
        ], $commandTasks->getResults());
    }
}
