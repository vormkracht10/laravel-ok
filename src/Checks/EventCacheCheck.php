<?php

namespace Backstage\Laravel\OK\Checks;

use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class EventCacheCheck extends Check
{
    public function run(): Result
    {
        $result = Result::new();

        return app()->eventsAreCached()
            ? $result->ok('Events are cached')
            : $result->failed('Events are not cached');
    }
}
