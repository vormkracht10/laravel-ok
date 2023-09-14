<?php

namespace Vormkracht10\LaravelOK\Checks;

use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class ConfigCacheCheck extends Check
{
    public function run(): Result
    {
        $result = Result::new();

        return app()->configurationIsCached()
            ? $result->ok('Configuration is cached')
            : $result->failed('Configuration is not cached');
    }
}
