<?php

namespace Vormkracht10\LaravelOK\Events;

use Vormkracht10\LaravelOK\Checks\Base\Check;

class CheckEnded
{
    public function __construct(public Check $check)
    {
    }
}
