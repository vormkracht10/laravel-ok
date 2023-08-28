<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Facades\Process;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

final class ComposerAuditCheck extends Check
{
    public string $view = 'ok::audit-check.composer-audit';

    protected array $with;

    public function run(): Result
    {
        $data = $this->with['advisories'] ?? $this->data()['advisories'];

        if (! ($count = count($data)) > 0) {
            return Result::new()
                ->ok('Found no vulnerabilities for your dependencies in Composer.');
        }

        $this->data = ['vulnerabilities' => $data];

        return Result::new()
            ->failed("Found $count vulnerabilities for your dependencies in Composer.");
    }

    public function with(array $data): self
    {
        $this->with = $data;

        return $this;
    }

    private function data(): array
    {
        return json_decode(
            Process::run('composer audit --format=json')->output(),
            true,
        );
    }
}
