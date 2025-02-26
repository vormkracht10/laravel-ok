<?php

namespace Backstage\Laravel\OK\Checks;

use Carbon\Carbon;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;
use Backstage\Laravel\OK\Checks\Traits\ReadsBootTimes;

class UptimeCheck extends Check
{
    use ReadsBootTimes;

    protected Carbon $maxTimeSinceRebootTimestamp;

    public function setMaxTimeSinceRebootTimestamp(Carbon $time): static
    {
        $this->maxTimeSinceRebootTimestamp = $time;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $timestamp = $this->getSystemUptime();

        if (! isset($this->maxTimeSinceRebootTimestamp)) {
            throw new \Exception('The max time since reboot was not set.');
        }

        if ($this->maxTimeSinceRebootTimestamp > $timestamp) {
            return $result->failed("Last reboot was at [{$timestamp->format('Y-m-d H:i')}], the maximum uptime for this server was set to [{$this->maxTimeSinceRebootTimestamp}]");
        }

        return $result->ok("Last reboot {$timestamp->diffInDays()} days and {$timestamp->diffInMinutes()} ago");
    }
}
