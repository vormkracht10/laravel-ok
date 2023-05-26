<?php

namespace Vormkracht10\LaravelOK\Events;

use Vormkracht10\LaravelOK\Checks\Check;

class CheckStarted
{
    public function __construct(public Check $check)
    {
    }
}
