<?php

namespace Backstage\Laravel\OK\Checks;

use Illuminate\Support\Facades\Process;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class PingCheck extends Check
{
    protected string $address = 'www.google.com';

    protected int $waitTime = 100;

    public function address(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function maxTimeout(int $milliseconds): static
    {
        $this->waitTime = $milliseconds;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $process = Process::run("ping {$this->address} -c 1 -W {$this->waitTime}");

        if (! $process->successful()) {
            throw new \Exception($process->errorOutput());
        }

        return ! str_contains($output = $process->output(), '1 packets out of wait time') && str_contains($output, '1 packets received')
            ? $result->ok("Application is able to ping address [{$this->address}]")
            : $result->failed("Application is unable to ping address [{$this->address}],");
    }
}
