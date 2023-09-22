<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Facades\Redis;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class RedisCheck extends Check
{
    protected ?string $connection = null;

    public function withConnection(string $connection): static
    {
        $this->connection = $connection;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        try {
            Redis::connection()->{'PING'}() !== 'PONG' ?? throw new \Exception;
        } catch (\Exception) {
            return $result->failed('Could not connect to Redis');
        }

        return $result->ok('Redis responded with PONG');
    }
}
