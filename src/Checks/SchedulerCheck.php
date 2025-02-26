<?php

namespace Backstage\Laravel\OK\Checks;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class SchedulerCheck extends Check
{
    protected string $cacheKey = 'laravel-ok:scheduler-check:heartbeat';

    protected string $cacheDriver;

    protected int $maxHeartbeatTimeout = 1;

    public function cacheDriver(string $name): static
    {
        $this->cacheDriver = $name;

        return $this;
    }

    public function setMaxHeartbeatTimeout(int $maxHeartbeatTimeout): static
    {
        $this->maxHeartbeatTimeout = $maxHeartbeatTimeout;

        return $this;
    }

    public function getCacheDriver(): string
    {
        return $this->cacheDriver ?? config('cache.default');
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function run(): Result
    {
        $result = Result::new();

        $lastHeartbeat = Cache::driver($this->getCacheDriver())->get($this->getCacheKey());

        if (is_null($lastHeartbeat)) {
            return $result->failed('The schedule has not yet run.');
        }

        $timestamp = Carbon::createFromTimestamp($lastHeartbeat);

        $passed = round($timestamp->diffInSeconds() / 60, 1);

        if ($passed >= $this->maxHeartbeatTimeout) {
            return $result->failed("The last heartbeat from the scheduler was {$passed} minutes ago");
        }

        return $result->ok("The last heartbeat from the scheduler was {$passed} minutes ago");
    }
}
