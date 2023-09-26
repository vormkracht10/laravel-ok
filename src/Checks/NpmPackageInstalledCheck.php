<?php

namespace Vormkracht10\LaravelOK\Checks;

use Symfony\Component\Process\Process;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class NpmPackageInstalledCheck extends Check
{
    protected array $shouldHave = [];

    protected array $with = [];

    public function shouldHave(array $packages): self
    {
        $this->shouldHave = $packages;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $installedPackages = $this->data();

        $missingPackages = array_diff($this->shouldHave, array_keys($installedPackages));

        if (count($missingPackages) === 0) {
            return $result->ok();
        }

        $missingPackages = implode(', ', $missingPackages);

        return $result->failed("The following packages are missing: {$missingPackages}");
    }

    protected function data()
    {
        return $this->with ?? json_decode(
            new Process(['npm', 'list', '--depth=0', '--json'])->run()->getOutput(),
            true,
        )['dependencies'];
    }
}
