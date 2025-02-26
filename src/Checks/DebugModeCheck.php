<?php

namespace Backstage\Laravel\OK\Checks;

use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

use function config;

class DebugModeCheck extends Check
{
    protected bool $shouldBe = false;

    public function shouldBe(bool $bool): self
    {
        $this->shouldBe = $bool;

        return $this;
    }

    protected function convertToText(bool $boolean): string
    {
        return $boolean ? 'enabled' : 'disabled';
    }

    public function run(): Result
    {
        $currentValue = config('app.debug');

        $result = Result::new();

        $shouldBeText = $this->convertToText((bool) $this->shouldBe);
        $currentText = $this->convertToText((bool) $currentValue);

        return $this->shouldBe === $currentValue
            ? $result->ok()
            : $result->failed("The debug mode should be {$shouldBeText}, but currently is {$currentText}");
    }
}
