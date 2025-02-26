<?php

namespace Backstage\Laravel\OK\Checks;

use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

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
