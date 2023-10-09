<?php

namespace Vormkracht10\LaravelOK;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class OK
{
    protected array $checks = [];

    public function checks(array $checks): self
    {
        $this->checks = array_merge($this->checks, $checks);

        return $this;
    }

    public function lastRun(string $check): Carbon
    {
        $timestamp = Cache::driver('file')->get("laravel-ok::runs::{$check}");

        return Carbon::createFromTimestamp($timestamp);
    }

    public function configuredChecks()
    {
        return collect($this->checks);
    }
}
