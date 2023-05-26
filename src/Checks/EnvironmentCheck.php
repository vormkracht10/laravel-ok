<?php

namespace Vormkracht10\LaravelOK\Checks\Checks;

use Vormkracht10\LaravelOK\Checks\Check;
use Vormkracht10\LaravelOK\Checks\Result;

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

        $result = Result::make();

        return $this->shouldBe === $currentValue
            ? $result->ok()
            : $result->failed("The environment should be {$this->shouldBe}, but currently is {$currentValue}");
    }
}
