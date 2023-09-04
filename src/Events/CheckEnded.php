<?php

namespace Vormkracht10\LaravelOK\Events;

use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class CheckEnded
{
    public function __construct(public Check $check, public Result $result)
    {
    }
}
