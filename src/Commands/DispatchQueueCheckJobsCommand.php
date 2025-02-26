<?php

namespace Backstage\Laravel\OK\Commands;

use Illuminate\Console\Command;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\QueueCheck;
use Backstage\Laravel\OK\Facades\OK;
use Backstage\Laravel\OK\Jobs\QueueHeartbeatJob;

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
