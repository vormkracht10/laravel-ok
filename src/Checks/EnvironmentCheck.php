<?php

namespace Backstage\Laravel\OK\Checks;

use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class EnvironmentCheck extends Check
{
    protected string $shouldBe = 'production';

    public function shouldBe(string $value): self
    {
        $this->shouldBe = $value;

        return $this;
    }

    public function run(): Result
    {
        $currentValue = app()->environment();

        $result = Result::new();

        return $this->shouldBe === $currentValue
            ? $result->ok()
            : $result->failed("The environment should be {$this->shouldBe}, but currently is {$currentValue}");
    }
}
