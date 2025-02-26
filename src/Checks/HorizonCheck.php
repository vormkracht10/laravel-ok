<?php

namespace Backstage\Laravel\OK\Checks;

use Illuminate\Support\Facades\Artisan;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

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
            $result->failed('Horizon is not running or is paused.');
    }
}
