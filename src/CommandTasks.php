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

    public function setOutput(OutputStyle $output) : self
    {
        $this->output = $output;

        return $this;
    }

    public function addTask($name, $result) : self
    {
        $this->results[$name] = $result;

        return $this;
    }

    public function outputResults() : void
    {
        collect($this->results)->each(function ($result, $description) {
            $this->output->writeLn(vsprintf('%s: %s', [
                $description,
                $result ? '<fg=green>✔</>' : '<fg=red>✘</>',
            ]));
        });
    }

    public function getResults() : array
    {
        return $this->results;
    }
}
