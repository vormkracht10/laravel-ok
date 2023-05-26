<?php

namespace Vormkracht10\LaravelOK\Checks\Traits;

use Vormkracht10\LaravelOK\Checks\Result;

trait ChecksDatabaseQueryCountResult
{
    public int $expectedCount = 0;

    public function checkExpectedCount(int $count)
    {
        return $this->expectedCount === $count;
    }

    public function run()
    {
        $currentCount = $this->queryCount();

        $result = Result::make();

        return $this->checkExpectedCount($currentCount)
            ? $result->ok()
            : $result->failed(
                $this->message() ?: "The database query count should be {$this->expectedCount}, but currently is {$currentCount}"
            );
    }
}
