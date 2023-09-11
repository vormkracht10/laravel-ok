<?php

namespace Vormkracht10\LaravelOK\Checks;

use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class RouteCacheCheck extends Check
{
    public function run(): Result
    {
        $result = Result::new();

        return app()->routesAreCached()
            ? $result->ok('routes are cached')
            : $result->failed('routes are not cached');
    }
}
