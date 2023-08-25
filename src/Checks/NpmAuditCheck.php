<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Facades\Process;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

final class NpmAuditCheck extends Check
{
    public string $view = 'ok::audit-check.npm-audit';

    public function run(): Result
    {
        $data = $this->data()['vulnerabilities'] ?? [];

        if (! ($count = count($data)) > 0) {
            return Result::new()
                ->ok('Found no vulnerabilities for your dependencies in NPM.');
        }

        $this->data = ['vulnerabilities' => $data];

        return Result::new()
            ->failed("Found $count vulnerabilities for your dependencies in NPM.");
    }

    private function data(): array
    {
        return json_decode(
            Process::run('npm audit --json')->output(),
            true,
        );
    }
}
