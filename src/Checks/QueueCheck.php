<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Facades\Cache;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class QueueCheck extends Check
{
    protected string $key = 'laravel-ok:queue-check:key';

    protected int $maxRuntime = 10;

    protected ?string $cacheDriver;

    protected array $onQueues = [];

    protected function failAfter(int $seconds): static
    {
        $this->maxRuntime = $seconds;

        return $this;
    }

    protected function cacheDriver(string $name): static
    {
        $this->cacheDriver = $name;

        return $this;
    }

    public function onQueues(array $queues): static
    {
        $this->onQueues = $queues;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        foreach ($this->onQueues as $queue) {
            $this->runQueue($queue);
        }

        $max = now()->addSeconds($this->maxRuntime);

        do {
            $pending = array_diff($this->onQueues, $this->ranQueues());

            if ($pending) {
                sleep(1);
                continue;
            }

            foreach ($this->onQueues as $queue) {
                Cache::driver($this->getCacheDriver())->forget("{$this->key}:{$queue}");
            }

            return $result->ok('all queues ran');
        } while (now() < $max);

        $failed = array_diff($this->onQueues, $this->ranQueues());

        return $result->failed('some queues didn\'t run: ' . implode(', ', $failed));
    }

    public function getCacheDriver(): string
    {
        return $this->cacheDriver ?? config('cache.default', 'array');
    }

    public function ranQueues(): array
    {
        return array_filter(
            $this->onQueues,
            fn($queue) => Cache::driver($this->getCacheDriver())->get("{$this->key}:{$queue}", false) == true,
        );
    }

    public function runQueue(string $name): void
    {
        $key = "{$this->key}:{$name}";
        $driver = $this->getCacheDriver();
        $ttl = $this->maxRuntime;

        dispatch(function () use ($key, $driver, $ttl) {
            Cache::driver($driver)->put($key, true, $ttl);
        })->onQueue($name);
    }
}
