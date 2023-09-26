<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Facades\Process;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class NpmPackageInstalledCheck extends Check
{
    protected array $shouldHave = [];

    public function shouldHave(array $packages): self
    {
        $this->shouldHave = $packages;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $installedPackages = $this->getInstalledPackages();

        dd($installedPackages);

        $missingPackages = array_diff($this->shouldHave, array_keys($installedPackages));

        if (count($missingPackages) === 0) {
            return $result->ok();
        }

        $missingPackages = implode(', ', $missingPackages);

        return $result->failed("The following packages are missing: {$missingPackages}");
    }

    private function getInstalledPackages()
    {
        $process = Process::fromShellCommandline('npm list --depth=0 --json');

        $process->run();

        $output = $process->getOutput();

        $json = json_decode($output, true);

        return $json['dependencies'];
    }
}
