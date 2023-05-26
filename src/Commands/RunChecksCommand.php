<?php

namespace Vormkracht10\LaravelOK\Commands;

use Illuminate\Console\Command;

class RunChecksCommand extends Command
{
    public $signature = 'ok:check';

    public $description = 'Runs all checks to be sure everything is OK';

    public function handle(): int
    {
        $this->info('Running checks...');

        $this->comment('All done');

        return self::SUCCESS;
    }
}
