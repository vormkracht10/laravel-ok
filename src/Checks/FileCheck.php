<?php

namespace Vormkracht10\LaravelOK\Checks;

use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class FileCheck extends Check
{
    protected array $files = [];

    /**
     * @param  array<int, string>  $files
     * @return $this
     */
    public function files(array $files): static
    {
        $this->files = $files;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        if (empty($files = $this->files)) {
            return $result->failed('No files have been configured');
        }

        $paths = array_map(
            fn ($path) => str_starts_with($path, '/') ? $path : base_path($path),
            $files,
        );

        $failed = array_filter(
            $paths,
            fn ($path) => ! file_exists($path),
        );

        return empty($failed)
            ? $result->ok('All paths are files')
            : $result->failed('Some paths don\'t exist or are not files ['.implode(', ', $failed).']');
    }
}
