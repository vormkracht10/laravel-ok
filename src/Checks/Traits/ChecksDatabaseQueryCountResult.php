<?php

namespace Vormkracht10\LaravelOK\Checks\Traits;

use Vormkracht10\LaravelOK\Checks\Base\Result;

trait ChecksDatabaseQueryCountResult
{
    public ?int $expectedCount = null;

    public ?int $minCount = null;

    public ?int $maxCount = null;

    public function checkExpectedCount(int $count)
    {
        if (! is_null($this->expectedCount)) {
            return $this->expectedCount === $count;
        }

        if (! is_null($this->minCount)) {
            return $count >= $this->minCount;
        }

        if (! is_null($this->maxCount)) {
            return $count <= $this->maxCount;
        }

        return false;
    }

    public function expectedCount(int $count)
    {
        $this->expectedCount = $count;

        return $this;
    }

    public function minCount(int $count)
    {
        $this->minCount = $count;

        return $this;
    }

    public function maxCount(int $count)
    {
        $this->maxCount = $count;

        return $this;
    }

    public function run(): Result
    {
        $currentCount = $this->queryCount();

        $result = Result::new();

        return $this->checkExpectedCount($currentCount)
            ? $result->ok()
            : $result->failed(
                $this->getMessage() ?: "The database query count should be {$this->expectedCount}, but currently is {$currentCount}"
            );
    }
}
