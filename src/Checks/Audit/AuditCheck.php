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
        if (isset($this->with)) {
            return $this->with;
        }

        $process = Process::run($this->getFullCommand());

        if (empty(json_decode($process->output(), true))) {
            dd($process->errorOutput());
        }

        return json_decode(
            $process->output(),
            true,
        );
    }
}
