<?php

namespace Backstage\Laravel\OK\Interfaces;

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
