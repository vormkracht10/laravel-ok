<?php

namespace Backstage\Laravel\OK\Checks;

use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\Facades\Redis;
use RuntimeException;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class RedisMemoryUsageCheck extends Check
{
    protected int $threshold;

    public function setThresholdGigabytes($amount): static
    {
        return $this->setThreshold($amount * (10 ** 9));
    }

    public function setThresholdMegabytes($amount): static
    {
        return $this->setThreshold($amount * (10 ** 6));
    }

    public function setThreshold(int $bytes): static
    {
        $this->threshold = $bytes;

        return $this;
    }

    public function run(): Result
    {
        if (! isset($this->threshold)) {
            throw new RuntimeException('Threshold for RedisMemoryUsageCheck is not set');
        }

        $result = Result::new();

        $bytes = $this->memoryUsageInBytes();

        $readable = round($bytes / (10 ** 6), 2);
        $readableMax = $this->threshold / (10 ** 6);

        if ($bytes >= $this->threshold) {
            return $result->failed("Memory usage is {$readable}MB, max is configured at {$readableMax}MB");
        }

        return $result->ok("Memory usage is at {$readable}MB");
    }

    protected function memoryUsageInBytes(): int
    {
        $redis = Redis::connection();

        return match ($redis::class) {
            PhpRedisConnection::class => $redis->info()['used_memory'],
            PredisConnection::class => $redis->info()['Memory']['used_memory'],
            default => null,
        };
    }
}
