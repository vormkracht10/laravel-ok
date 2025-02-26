<?php

namespace Backstage\Laravel\OK\Checks\Audit;

use Backstage\Laravel\OK\Checks\Base\Result;

final class ComposerAuditCheck extends AuditCheck
{
    public string $view = 'ok::audit-check.composer-audit';

    protected string $command = 'composer audit --format=json';

    public function run(): Result
    {
        $data = $this->data()['advisories'];

        if (! ($count = count($data)) > 0) {
            return Result::new()
                ->ok('Found no vulnerabilities for your dependencies in Composer');
        }

        $this->data = ['vulnerabilities' => $data];

        return Result::new()
            ->failed("Found {$count} vulnerabilities for your dependencies in Composer");
    }
}
