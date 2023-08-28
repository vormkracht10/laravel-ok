<?php

namespace Vormkracht10\LaravelOK\Checks\Audit;

use Exception;
use Illuminate\Support\Facades\Process;
use Vormkracht10\LaravelOK\Checks\Base\Check;

abstract class AuditCheck extends Check
{
    protected string $command;

    protected array $with;

    public function getCommand(): string
    {
        if (! isset($this->command)) {
            throw new Exception('No command set');
        }

        return $this->command;
    }

    protected function getFullCommand(): string
    {
        $include = config('ok.checks.audit.path', []);

        $PATH = 'PATH=$PATH:';

        foreach ($include as $bin) {
            $PATH .= "$bin:";
        }

        $PATH = rtrim($PATH, ':');

        return "$PATH {$this->getCommand()}";
    }

    public function with(array $data): static
    {
        $this->with = $data;

        return $this;
    }

    protected function data()
    {
        return $this->with ?? json_decode(
            Process::run($this->getFullCommand())->output(),
            true,
        );
    }
}
