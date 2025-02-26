<?php

namespace Backstage\Laravel\OK\Checks;

use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class RouteCacheCheck extends Check
{
    public function run(): Result
    {
        $result = Result::new();

        return app()->routesAreCached()
            ? $result->ok('Routes are cached')
            : $result->failed('Routes are not cached');
    }
}
