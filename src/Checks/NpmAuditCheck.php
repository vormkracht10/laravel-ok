<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Facades\Process;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Exceptions\Checks\Audit\NpmNoLock;

final class NpmAuditCheck extends Check
{
    public string $view = 'ok::audit-check.npm-audit';

    protected array $with;

    /**
     * @throws NpmNoLock
     */
    public function run(): Result
    {
        $data = $this->with ?? $this->data();

        if (! ($count = count($data['vulnerabilities'])) > 0) {
            return Result::new()
                ->ok('Found no vulnerabilities for your dependencies in NPM.');
        }

        $this->data = $data;

        return Result::new()
            ->failed("Found {$data['metadata']['vulnerabilities']['total']} vulnerabilities for your dependencies in NPM.");
    }

    public function with(array $data): self
    {
        $this->with = $data;

        return $this;
    }

    /**
     * @throws NpmNoLock
     */
    private function data(): array
    {
        $process = Process::run('npm audit --json');

        if ($process->exitCode() == 1 && str_contains($process->errorOutput(), 'ENOLOCK')) {
            throw new NpmNoLock('no lock file found');
        }

        return json_decode(
            $process->output(),
            true,
        );
    }
}
