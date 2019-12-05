<?php

namespace Honeybadger\Tests;

use Honeybadger\HoneybadgerLaravel\CommandTasks;
use Honeybadger\HoneybadgerLaravel\Exceptions\TaskFailed;
use Illuminate\Console\OutputStyle;

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
        $commandTasks->addTask('Example Task', function () {
            return true;
        });

        $commandTasks->runTasks();

        $this->assertEquals([
            'Example Task' => true,
        ], $commandTasks->getResults());
    }

    /** @test */
    public function outputs_unsuccessful_tasks()
    {
        $this->expectException(TaskFailed::class);
        $output = $this->createMock(OutputStyle::class);

        $output->expects($this->once())
            ->method('writeLn')
            ->with('Example Task: <fg=red>✘</>');

        $commandTasks = new CommandTasks;
        $commandTasks->setOutput($output);
        $commandTasks->addTask('Example Task', function () {
            return false;
        });

        $commandTasks->runTasks();

        $this->assertEquals([
            'Example Task' => false,
        ], $commandTasks->getResults());
    }

    /** @test */
    public function outputs_multiple_tasks()
    {
        $this->expectException(TaskFailed::class);
        $output = $this->createMock(OutputStyle::class);

        $output->expects($this->exactly(2))
            ->method('writeLn')
            ->withConsecutive(
                ['Example successful task: <fg=green>✔</>'],
                ['Example failed task: <fg=red>✘</>']
            );

        $commandTasks = new CommandTasks;
        $commandTasks->setOutput($output);
        $commandTasks->addTask('Example successful task', function () {
            return true;
        });
        $commandTasks->addTask('Example failed task', function () {
            return false;
        });

        $commandTasks->runTasks();

        $this->assertEquals([
            'Example successful task' => true,
            'Example failed task' => false,
        ], $commandTasks->getResults());
    }

    /** @test */
    public function whether_any_tasks_have_failed()
    {
        $this->expectException(TaskFailed::class);

        $commandTasks = new CommandTasks;
        $commandTasks->addTask('Example successful task', function () {
            return true;
        });
        $commandTasks->addTask('Example failed task', function () {
            return false;
        });

        $commandTasks->runTasks();

        $this->assertTrue($commandTasks->hasFailedTasks());
    }

    /** @test */
    public function whether_any_tasks_have_passed()
    {
        $commandTasks = new CommandTasks;
        $commandTasks->addTask('Example successful task', function () {
            return true;
        });

        $commandTasks->runTasks();

        $this->assertFalse($commandTasks->hasFailedTasks());
    }
}
