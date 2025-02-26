<?php

namespace Backstage\Laravel\OK\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\SchedulerCheck;
use Backstage\Laravel\OK\Facades\OK;

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
