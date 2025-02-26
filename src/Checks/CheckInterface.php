<?php

namespace App\Checks;

use Backstage\Laravel\OK\Checks\Base\Result;

interface CheckInterface
{
    public function run(): Result;
}
