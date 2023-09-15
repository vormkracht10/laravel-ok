<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\Facades\Redis;
use RuntimeException;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class RedisMemoryUsageCheck extends Check
{
    protected int $threshold;

    public function gigabytes($amount): static
    {
        return $this->threshold($amount * (10 ** 9));
    }

    public function megabytes($amount): static
    {
        return $this->threshold($amount * (10 ** 6));
    }

    public function threshold(int $bytes): static
    {
        $this->threshold = $bytes;

        return $this;
    }

    public function run(): Result
    {
        if (! isset($this->threshold)) {
            throw new RuntimeException('threshold for RedisMemoryUsageCheck is not set');
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

    public function memoryUsageInBytes(): int
    {
        $redis = Redis::connection();

        return match ($redis::class) {
            PhpRedisConnection::class => $redis->info()['used_memory'],
            PredisConnection::class => $redis->info()['Memory']['used_memory'],
            default => null,
        };
    }
}
