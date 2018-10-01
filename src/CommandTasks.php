<?php

namespace Honeybadger\HoneybadgerLaravel;

class CommandTasks
{
    protected $output;
    protected $results = [];

    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    public function addTask($name, $result)
    {
        $this->results[$name] = $result;
    }

    public function outputResults()
    {
        collect($this->results)->each(function ($result, $description) {
            $this->output->writeLn(vsprintf('%s: %s', [
                $description,
                $result ? '<fg=green>✔</>' : '<fg=red>✘</>',
            ]));
        });
    }

    public function getResults()
    {
        return $this->results;
    }
}
