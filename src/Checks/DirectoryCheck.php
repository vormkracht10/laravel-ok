<?php

namespace Vormkracht10\LaravelOK\Checks;

use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class DirectoryCheck extends Check
{
    protected array $directories = [];

    public function directories(array $directories): static
    {
        $this->directories = $directories;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $failed = [];

        foreach ($this->directories as $directory) {
            $path = str_starts_with($directory, '/') ? $directory : base_path($directory);

            if (! is_dir($path)) {
                $failed[] = $directory;
            }
        }

        if (! empty($failed)) {
            return $result->failed('Some configured paths are not directories or do not exist: ['.implode(', ', $failed).']');
        }

        return $result->ok('All configured paths exist and are directories');
    }
}
