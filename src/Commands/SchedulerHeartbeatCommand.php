<?php

namespace Vormkracht10\LaravelOK\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\SchedulerCheck;
use Vormkracht10\LaravelOK\Facades\OK;

class SchedulerHeartbeatCommand extends Command
{
    protected $signature = 'ok:scheduler-heartbeat';

    protected $description = 'Command description';

    public function handle(): int
    {
        /**
         * @var SchedulerCheck|null $check
         */
        $check = OK::configuredChecks()->first(
            fn (Check $check) => $check instanceof SchedulerCheck,
        );

        if (! $check) {
            return static::INVALID;
        }

        $driver = $check->getCacheDriver();
        $key = $check->getCacheKey();

        Cache::driver($driver)->put($key, now()->timestamp);

        return static::SUCCESS;
    }
}
