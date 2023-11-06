<?php

namespace Vormkracht10\LaravelOK\Checks;

use Exception;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class MemoryUsageCheck extends Check
{
    // Runs on Linux only.

    protected int $limit = 90;

    public function threshold(int $percentage): static
    {
        $this->limit = $percentage;

        return $this;
    }

    public function getSystemMemInfo()
    {
        $data = explode("\n", file_get_contents('/proc/meminfo'));
        $memInfo = [];

        foreach ($data as $line) {
            [$key, $val] = explode(':', $line);
            $memInfo[$key] = explode(' ', trim($val), 2)[0];
        }

        return $memInfo;
    }

    public function run(): Result
    {
        $result = Result::new();

        if (PHP_OS !== 'Linux') {
            return $result->failed('The MemoryUsageCheck only works on Linux.');
        }

        $data = $this->getSystemMemInfo();

        $usedPercentage = (int) $data['MemTotal'] !== 0
            ? round(100 - (($data['MemAvailable'] / $data['MemTotal']) * 100), 2)
            : false;

        if ($usedPercentage === false) {
            return $result->failed('Failed to measure memory usage.');
        }

        if ($usedPercentage > $this->limit) {
            return $result->failed("Memory usage is at {$usedPercentage}%, limit is configured to {$this->limit}%");
        }

        return $result->ok("Memory usage is at {$usedPercentage}");
    }
}
