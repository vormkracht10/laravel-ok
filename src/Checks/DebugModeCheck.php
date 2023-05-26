<?php

namespace Vormkracht10\LaravelOK\Checks\Checks;

use function config;
use Vormkracht10\LaravelOK\Checks\Result;

class DebugModeCheck extends Check
{
    protected bool $expected = false;

    public function expectedToBe(bool $bool): self
    {
        $this->expected = $bool;

        return $this;
    }

    public function run(): Result
    {
        $actual = config('app.debug');

        $result = Result::make();

        return $this->expected === $actual
            ? $result->ok()
            : $result->failed("The debug mode was expected to be `{$this->convertToWord((bool) $this->expected)}`, but actually was `{$this->convertToWord((bool) $actual)}`");
    }

    protected function convertToWord(bool $boolean): string
    {
        return $boolean ? 'true' : 'false';
    }
}
