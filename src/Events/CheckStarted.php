<?php

namespace Backstage\Laravel\OK\Events;

use Backstage\Laravel\OK\Checks\Base\Check;

class CheckStarted
{
    public function __construct(public Check $check)
    {
    }
}
