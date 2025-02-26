<?php

namespace Backstage\Laravel\OK\Checks\Audit;

use Exception;
use Illuminate\Support\Facades\Process;
use Backstage\Laravel\OK\Checks\Base\Check;

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

    private function isWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
    }

    protected function getPath(): string
    {
        $include = config('ok.checks.audit.path', []);

        if ($this->isWindows()) {
            $PATH = 'set PATH=%PATH%;';

            foreach ($include as $bin) {
                $PATH .= "$bin;";
            }

            return $PATH;
        } else {
            $PATH = 'PATH=$PATH:';

            foreach ($include as $bin) {
                $PATH .= "$bin:";
            }

            $PATH = rtrim($PATH, ':');
        }

        return $PATH;
    }

    protected function getFullCommand(): string
    {
        return "{$this->getPath()} {$this->getCommand()}";
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
