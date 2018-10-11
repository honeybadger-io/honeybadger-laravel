<?php

namespace Honeybadger\HoneybadgerLaravel;

use Illuminate\Console\OutputStyle;

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
     * Set command output.
     *
     * @param  \Illuminate\Console\OutputStyle  $output
     * @return self
     */
    public function setOutput(OutputStyle $output) : self
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Add task with result to the stack.
     *
     * @param  string  $name
     * @param  bool  $result
     * @return self
     */
    public function addTask(string $name, bool $result) : self
    {
        $this->results[$name] = $result;

        return $this;
    }

    /**
     * Send results to the command output.
     *
     * @return void
     */
    public function outputResults() : void
    {
        collect($this->results)->each(function ($result, $description) {
            $this->output->writeLn(vsprintf('%s: %s', [
                $description,
                $result ? '<fg=green>✔</>' : '<fg=red>✘</>',
            ]));
        });
    }

    /**
     * Get the results of all tasks.
     *
     * @return array
     */
    public function getResults() : array
    {
        return $this->results;
    }
}
