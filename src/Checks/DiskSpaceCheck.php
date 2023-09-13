<?php

namespace Vormkracht10\LaravelOK\Checks;

use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class DiskSpaceCheck extends Check
{
    protected int $thresholdPercentage = 80;

    protected string $at;

    public function __construct()
    {
        $this->at = base_path();
    }

    public function threshold(int $percentage): static
    {
        $this->thresholdPercentage = $percentage;

        return $this;
    }

    public function directory(string $directory): static
    {
        $this->at = $directory;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $directory = $this->at;

        $usage = floor(100 - ((disk_free_space($directory) / disk_total_space($directory)) * 100));

        if ($usage > $this->thresholdPercentage) {
            return $result->failed("disk space usage is {$usage}%, threshold set at {$this->thresholdPercentage}%");
        }

        return $result->ok("disk space is usage is {$usage}%");
    }
}
