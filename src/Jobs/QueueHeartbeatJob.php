<?php

namespace Vormkracht10\LaravelOK\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Vormkracht10\LaravelOK\Checks\QueueCheck;

class QueueHeartbeatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected QueueCheck $check;

    public function retryUntil(): DateTime
    {
        return now()->addMinutes($this->check->maxHeartbeatDelay);
    }

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
