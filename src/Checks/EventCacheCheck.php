<?php

namespace Vormkracht10\LaravelOK\Checks;

use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class EventCacheCheck extends Check
{
    public function run(): Result
    {
        $result = Result::new();

        return app()->eventsAreCached()
            ? $result->ok('events are cached')
            : $result->failed('events are not cached');
    }
}
