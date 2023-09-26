<?php

namespace Vormkracht10\LaravelOK\Checks;

use Carbon\Carbon;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class UptimeCheck extends Check
{
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

        if ($this->maxTimeSinceRebootTimestamp > $timestamp) {
            return $result->failed("Last reboot was at [{$timestamp}], the maximum uptime for this server was set to [{$this->maxTimeSinceRebootTimestamp}]");
        }

        return $result->ok("Last reboot {$timestamp->diffInDays()} days and {$timestamp->diffInMinutes()} ago");
    }

    protected function getSystemUptime(): Carbon
    {
        return match (PHP_OS) {
            'Linux' => $this->getSystemUptimeLinux(),
            'Darwin' => $this->getSystemUptimeDarwin(),
        };
    }

    protected function runProcess(string $command): string
    {
        $process = Process::run($command);

        return match ($process->successful()) {
            true => $process->output(),
            false => throw new RuntimeException('Could not get system boot timestamp: '.$process->errorOutput()),
        };
    }

    protected function getSystemUptimeLinux(): Carbon
    {
        return Carbon::createFromTimestamp(
            $this->runProcess('date -d "$(who -b | awk \'{print $3, $4}\')" +%s'),
        );
    }

    protected function getSystemUptimeDarwin(): Carbon
    {
        return Carbon::createFromTimestamp(
            $this->runProcess('date -j -f "%b %d %H:%M" "$(who -b | awk \'{print $3,$4,$5}\')" +%s'),
        );
    }
}
