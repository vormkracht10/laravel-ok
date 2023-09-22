<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class RedisCheck extends Check
{
    /**
     * @var array<int, string>
     */
    protected array $connections = [];

    /**
     * @param  non-empty-array<int, string>|string  $connections
     */
    public function withConnections($connections): static
    {
        $this->connections = Arr::wrap($connections);

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $connections = empty($this->connections) ? [null] : $this->connections;

        foreach ($connections as $connection) {
            try {
                if (Redis::connection($connection)->{'PING'}('PONG') !== 'PONG') {
                    throw new \Exception;
                }
            } catch (\Exception) {
                return $result->failed("Could not connect to Redis with connection [{$connection}]");
            }
        }

        return $result->ok('Redis responded with PONG');
    }
}
