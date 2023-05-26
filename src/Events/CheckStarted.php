<?php

namespace Vormkracht10\LaravelOK\Events;

use Vormkracht10\LaravelOK\Checks\Base\Check;

class CheckStarted
{
    public function __construct(public Check $check)
    {
    }
}
