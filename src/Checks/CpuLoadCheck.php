<?php

namespace Vormkracht10\LaravelOK\Checks;

use Exception;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class CpuLoadCheck extends Check
{
    protected array $maxLoad;

    public function setMaxLoad(float $short = null, float $mid = null, float $long = null): static
    {
        $this->maxLoad = [$short, $mid, $long];

        return $this;
    }

    public function run(): Result
    {
        if (! isset($this->maxLoad)) {
            throw new Exception('The max average load was not set');
        }

        $result = Result::new();

        $load = sys_getloadavg();

        if (! $load) {
            return $result->failed('Failed to get system load averages');
        }

        foreach ($this->maxLoad as $index => $max) {
            if (is_null($max)) {
                continue;
            }

            if ($index == count($load)) {
                break;
            }

            if ($max < $actual = round($load[$index], 2)) {
                return $result->failed(
                    "System average load [{$index}] is at {$max}%, max is configured at {$actual}%",
                );
            }
        }

        return $result->ok('System load averages are ok');
    }
}
