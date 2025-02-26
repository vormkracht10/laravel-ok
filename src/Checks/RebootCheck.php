<?php

namespace Backstage\Laravel\OK\Checks;

use Carbon\Carbon;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;
use Backstage\Laravel\OK\Checks\Traits\ReadsBootTimes;

class RebootCheck extends Check
{
    use ReadsBootTimes;

    protected Carbon $minTimeSinceReboot;

    public function setMinTimeSinceReboot(Carbon $timestamp): static
    {
        $this->minTimeSinceReboot = $timestamp;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $timestamp = $this->getSystemUptime();

        if (! isset($this->minTimeSinceReboot)) {
            throw new \Exception('The minimum time since reboot was not set.');
        }

        if ($this->minTimeSinceReboot < $timestamp) {
            return $result->failed("Last reboot was at [{$this->minTimeSinceReboot->format('Y-m-d H:i')}], the minimum uptime for this server was set to {$this->minTimeSinceReboot->diffInMinutes()} minutes");
        }

        return $result->ok("Last reboot {$timestamp->diffInDays()} days and {$timestamp->diffInMinutes()} ago");
    }
}
