<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Facades\Artisan;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class HorizonCheck extends Check
{
    public function run(): Result
    {
        $result = Result::new();

        if (! class_exists(HorizonApplicationServiceProvider::class)) {
            return $result->failed('Horizon is not installed');
        }

        return Artisan::call('horizon:status') == 0 ?
            $result->ok('Horizon is running.') :
            $result->failed('Horizon is not running or paused.');
    }
}
