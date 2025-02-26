<?php

namespace Backstage\Laravel\OK\Checks;

use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class DiskSpaceCheck extends Check
{
    protected int $thresholdPercentage = 80;

    protected string $directory;

    public function __construct()
    {
        $this->directory = base_path();
    }

    public function threshold(int $percentage): static
    {
        $this->thresholdPercentage = $percentage;

        return $this;
    }

    public function directory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $usage = floor(100 - ((disk_free_space($this->directory) / disk_total_space($this->directory)) * 100));

        if ($usage > $this->thresholdPercentage) {
            return $result->failed("Disk space usage is {$usage}%, threshold set at {$this->thresholdPercentage}%");
        }

        return $result->ok("Disk space is usage is {$usage}%");
    }
}
