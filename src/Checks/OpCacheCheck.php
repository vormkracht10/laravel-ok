<?php

namespace Vormkracht10\LaravelOK\Checks;

use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class OpCacheCheck extends Check
{
    protected bool $checkJit = false;

    protected bool $checkCli = false;

    public function checkJit(): static
    {
        $this->checkJit = true;

        return $this;
    }

    public function checkCli(): static
    {
        $this->checkCli = true;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        if (! $this->opCacheIsConfigured()) {
            return $result->failed('OpCache is not configured to run.');
        }

        if (! $this->opCacheIsRunning()) {
            return $result->failed('OpCache is not running.');
        }

        if ($this->checkJit && ! $this->isJitEnabled()) {
            return $result->failed('OpCache JIT is not running.');
        }

        return $result->ok('OpCache is running.');
    }

    protected function opCacheIsConfigured(): bool
    {
        if (! function_exists('opcache_get_configuration')) {
            return false;
        }

        $configuration = opcache_get_configuration()['directives'];

        return $configuration['opcache.enable']
            && $this->checkCli ? $configuration['opcache.enable_cli'] : true;
    }

    protected function opCacheIsRunning(): bool
    {
        if (! function_exists('opcache_get_status')) {
            return false;
        }

        $configuration = opcache_get_status();

        return $configuration['opcache_enabled'] ?? false;
    }

    protected function isJitEnabled(): bool
    {
        if (! function_exists('opcache_get_status')) {
            return false;
        }

        $configuration = opcache_get_status()['jit'];

        return $configuration['enabled'] && $configuration['on']
            ?? false;
    }
}
