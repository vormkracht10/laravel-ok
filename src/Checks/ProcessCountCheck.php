<?php

namespace Backstage\Laravel\OK\Checks;

use Illuminate\Support\Facades\Process;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class ProcessCountCheck extends Check
{
    protected array $processCountThresholds = [];

    public function setProcessCountThresholds(array $config): static
    {
        $this->processCountThresholds = $config;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $failed = [];

        foreach ($this->processCountThresholds as $process => $max) {
            $count = $this->getProcessCountForName($process);

            if ($count > $max) {
                $failed[] = "{$process} ({$count})";
            }
        }

        if (! empty($failed)) {
            return $result->failed('Some commands have more processes running than should be allowed: ['.implode(', ', $failed).']');
        }

        return $result->ok('All processes are ok');
    }

    protected function getProcessCountForName(string $name): int
    {
        $process = Process::run("ps -e | awk '{print $4}' | sed 's:.*/::' | grep '^{$name}$'");

        if (! $process->successful()) {
            return 0;
        }

        $output = $process->output();

        return count(array_filter(explode("\n", $output)));
    }
}
