<?php

namespace Vormkracht10\LaravelOK\Checks;

use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class OpCacheCheck extends Check
{
    public function run(): Result
    {
        $result = Result::new();

        if (! $this->opCacheIsRunning()) {
            return $result->failed('OpCache is not running.');
        }

        return $result->ok('OpCache is running.');
    }

    protected function opCacheIsRunning(): bool
    {
        if (! function_exists('opcache_get_status')) {
            return false;
        }

        $configuration = opcache_get_status();

        return $configuration['opcache_enabled'] ?? false;
    }
}
