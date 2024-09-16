<?php

namespace Vormkracht10\LaravelOK\Interfaces;

use Illuminate\Console\Scheduling\CallbackEvent;

interface Scheduled
{
    /**
     * Define the schedule for this static cacher.
     *
     * @param  CallbackEvent  $callback
     * @return void
     */
    public static function schedule($callback);
}
