<?php

namespace Vormkracht10\LaravelOK;

class OK
{
    protected array $checks = [];

    public function checks(array $checks): self
    {
        $this->checks = array_merge($this->checks, $checks);

        return $this;
    }
}
