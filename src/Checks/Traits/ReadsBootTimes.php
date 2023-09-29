<?php

namespace Vormkracht10\LaravelOK\Checks\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Process;
use RuntimeException;

trait ReadsBootTimes
{
    protected function getSystemUptime(): Carbon
    {
        return match ($os = PHP_OS) {
            'Linux' => $this->getSystemUptimeLinux(),
            'Darwin' => $this->getSystemUptimeDarwin(),
            default => throw new RuntimeException("This os ({$os}) is not supported by the UptimeCheck"),
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
        )->roundMinute();
    }
}
