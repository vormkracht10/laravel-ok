<?php

namespace Vormkracht10\LaravelOK\Commands;

use Illuminate\Console\Command;

class LaravelOKCommand extends Command
{
    public $signature = 'laravel-ok';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
