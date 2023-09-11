<?php

namespace Vormkracht10\LaravelOK\Checks;

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
        $data = explode("\n", trim(file_get_contents('/proc/meminfo')));
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

        $data = $this->getSystemMemInfo();

        $usedPercentage = round(100 - (($data['MemAvailable'] / $data['MemTotal']) * 100), 2);

        if ($usedPercentage > $this->limit) {
            return $result->failed("memory usage is at {$usedPercentage}%, limit is configured to {$this->limit}%");
        }

        return $result->ok("memory usage is at {$usedPercentage}");
    }
}
