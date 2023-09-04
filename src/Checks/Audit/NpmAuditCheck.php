<?php

namespace Vormkracht10\LaravelOK\Checks\Audit;

use Vormkracht10\LaravelOK\Checks\Base\Result;

final class NpmAuditCheck extends AuditCheck
{
    public string $view = 'ok::audit-check.npm-audit';

    protected string $command = 'npm audit --json';

    public function run(): Result
    {
        $data = $this->data();

        if (! (count($data['vulnerabilities'])) > 0) {
            return Result::new()
                ->ok('Found no vulnerabilities for your dependencies in NPM.');
        }

        $this->data = $data;

        return Result::new()
            ->failed("Found {$data['metadata']['vulnerabilities']['total']} vulnerabilities for your dependencies in NPM.");
    }
}
