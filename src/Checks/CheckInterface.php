<?php 

namespace App\Checks;

use Vormkracht10\LaravelOK\Checks\Base\Result;

interface CheckInterface
{
    public function run(): Result;
}