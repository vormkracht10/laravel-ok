<?php

namespace Vormkracht10\LaravelOK\Commands;

use Illuminate\Console\Command;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\QueueCheck;
use Vormkracht10\LaravelOK\Facades\OK;
use Vormkracht10\LaravelOK\Jobs\QueueHeartbeatJob;

class DispatchQueueCheckJobsCommand extends Command
{
    protected $signature = 'ok:queue-check-jobs';

    public function handle(): int
    {
        $checks = OK::configuredChecks()->filter(fn (Check $check) => $check instanceof QueueCheck);

        /**
         * @var QueueCheck $check
         */
        foreach ($checks as $check) {
            foreach ($check->getQueues() as $queue) {
                QueueHeartbeatJob::dispatch($check)->onQueue($queue);
            }
        }

        return static::SUCCESS;
    }
}
