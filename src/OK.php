<?php

namespace Backstage\Laravel\OK;

use Illuminate\Support\Collection;

class OK
{
    protected array $checks = [];

    public function checks(array $checks): self
    {
        $this->checks = array_merge($this->checks, $checks);

        return $this;
    }

    public function configuredChecks(): Collection
    {
        return collect($this->checks);
    }
}
