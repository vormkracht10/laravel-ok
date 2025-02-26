<?php

namespace Backstage\Laravel\OK\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Backstage\Laravel\OK\Checks\QueueCheck;

class QueueHeartbeatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected QueueCheck $check;

    public function __construct(QueueCheck $check)
    {
        $this->check = $check;
    }

    public function handle(): void
    {
        $key = $this->check->getCacheKey($this->queue);

        Cache::driver($this->check->getCacheDriver())
            ->set($key, now()->timestamp);
    }
}
