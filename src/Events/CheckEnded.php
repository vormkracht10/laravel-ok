<?php

namespace Backstage\Laravel\OK\Events;

use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class CheckEnded
{
    public function __construct(public Check $check, public Result $result)
    {
    }
}
