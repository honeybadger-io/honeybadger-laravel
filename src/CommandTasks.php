<?php

namespace Honeybadger\HoneybadgerLaravel;

use Honeybadger\HoneybadgerLaravel\Exceptions\TaskFailed;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;

class CommandTasks
{
    /**
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var array
     */
    protected $tasks = [];

    /**
     * @var bool
     */
    protected $throwOnError = true;

    /**
     * Set command output.
     *
     * @param  \Illuminate\Console\OutputStyle  $output
     * @return self
     */
    public function setOutput(OutputStyle $output): self
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Add task with result to the stack.
     *
     * @param  string  $name
     * @param  callable  $task
     * @return self
     */
    public function addTask(string $name, callable $task, bool $throwOnFail = false): self
    {
        $this->tasks[$name] = [
            'task' => $task,
            'throw_on_fail' => $throwOnFail,
        ];

        return $this;
    }

    /**
     * Send tasks to the command output.
     *
     * @return void
     *
     * @throws \Honeybadger\HoneybadgerLaravel\TaskFailed
     */
    public function runTasks(): void
    {
        Collection::make($this->tasks)->each(function ($task, $description) {
            $result = $task['task']();

            if ($this->output) {
                $this->output->writeLn(vsprintf('%s: %s', [
                    $description,
                    $result ? '<fg=green>✔</>' : '<fg=red>✘</>',
                ]));
            }

            $this->results[$description] = $result;

            if (! $result && $task['throw_on_fail'] && $this->throwOnError) {
                throw new TaskFailed(sprintf('%s failed, please review output and try again.', $description));
            }
        });
    }

    /**
     * Get all task results.
     *
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return bool
     */
    public function hasFailedTasks(): bool
    {
        return in_array(false, $this->results);
    }

    /**
     * @return self
     */
    public function doNotThrowOnError(): self
    {
        $this->throwOnError = false;

        return $this;
    }
}
