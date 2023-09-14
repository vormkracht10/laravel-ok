<?php

namespace Vormkracht10\LaravelOK\Checks;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class QueueCheck extends Check
{
    protected string $cacheKey = 'laravel-ok:queue-check:key';

    protected string $cacheDriver;

    protected int $maxHeartbeatTimeout = 10;

    protected array $onQueues = [];

    public function cacheDriver(string $driver): static
    {
        $this->cacheDriver = $driver;

        return $this;
    }

    public function getCacheDriver(): string
    {
        return $this->cacheDriver ?? config('cache.default');
    }

    public function maxHeartbeatDelay(int $minutes): static
    {
        $this->maxHeartbeatTimeout = $minutes;

        return $this;
    }

    public function getCacheKey(string $queue): string
    {
        return "{$this->cacheKey}:{$queue}";
    }

    public function queues(array $queues): static
    {
        $this->onQueues = array_unique($queues);

        return $this;
    }

    public function getQueues(): array
    {
        return $this->onQueues;
    }

    public function run(): Result
    {
        $result = Result::new();

        $failed = [];

        foreach ($this->getQueues() as $queue) {
            $lastHeartbeat = Cache::driver($this->getCacheDriver())->get($this->getCacheKey($queue));

            if (is_null($lastHeartbeat)) {
                $failed[] = $queue;
                continue;
            }

            $timestamp = Carbon::createFromTimestamp($lastHeartbeat);

            $lastRun = $timestamp->diffInMinutes();

            if ($lastRun > $this->maxHeartbeatTimeout) {
                $failed[] = $queue;
            }
        }

        if (! empty($failed)) {
            return $result->failed('There were issues with some queues: ' . implode(', ', $failed));
        }

        return $result->ok('All queues are doing fine.');
    }
}
