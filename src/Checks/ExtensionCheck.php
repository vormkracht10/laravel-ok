<?php

namespace Backstage\Laravel\OK\Checks;

use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class ExtensionCheck extends Check
{
    protected array $extensions = [];

    public function extensions(array $extensions): static
    {
        $this->extensions = $extensions;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $loaded = get_loaded_extensions();

        if (! empty($diff = array_diff($this->extensions, $loaded))) {
            return $result->failed('Some extensions aren\'t loaded: '.implode(', ', $diff));
        }

        return $result->ok('All specified extensions are loaded');
    }
}
