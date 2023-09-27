<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Facades\Process;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class ProcessCountCheck extends Check
{
    protected array $processCountThresholds = [];

    public function setProcessCountThresholds(array $config): static
    {
        $this->processCountThresholds = $this->processConfig($config);

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $failed = [];

        foreach ($this->processCountThresholds as $process => $config) {
            $count = $this->getProcessCountForName($process, $config['exact'] ?? true);

            if ($count > $config['max']) {
                $failed[] = "{$process} ({$count})";
            }
        }

        if (! empty($failed)) {
            return $result->failed('Some commands have more processes running than should be allowed: ['.implode(', ', $failed).']');
        }

        return $result->ok('All processes are ok');
    }

    protected function processConfig(array $config): array
    {
        $reconstructed = [];

        foreach ($config as $process => $processConfig) {
            if (! is_array($processConfig) && ! is_int($processConfig)) {
                throw new \Exception('The config for each process has to be either an integer (max process count) or an array');
            }

            if (is_int($processConfig)) {
                $processConfig = ['max' => $processConfig];
            }

            if (! isset($processConfig['max']) || ! is_int($processConfig['max'])) {
                throw new \Exception('The configured max process count has to be an integer');
            }

            $reconstructed[$process] = $processConfig;
        }

        return $reconstructed;
    }

    protected function getProcessCountForName(string $name, bool $exact = true): int
    {
        $command = $exact
            ? "ps -e | awk '{print $4}' | sed 's:.*/::' | grep '^{$name}$'"
            : "ps -e | awk '{print $4}' | sed 's:.*/::' | grep '{$name}'";

        $process = Process::run($command);

        if (! $process->successful()) {
            return 0;
        }

        $output = $process->output();

        return count(array_filter(explode("\n", $output)));
    }
}
