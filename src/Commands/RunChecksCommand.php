<?php

namespace Vormkracht10\LaravelOK\Commands;

use Exception;
use Illuminate\Console\Command;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Enums\Status;
use Vormkracht10\LaravelOK\Events\CheckEnded;
use Vormkracht10\LaravelOK\Events\CheckFailed;
use Vormkracht10\LaravelOK\Events\CheckStarted;
use Vormkracht10\LaravelOK\Exceptions\CheckDidNotComplete;
use Vormkracht10\LaravelOK\Facades\OK;

class RunChecksCommand extends Command
{
    public $signature = 'ok:check';

    public $description = 'Runs all checks to be sure everything is OK';

    protected array $thrownExceptions = [];

    public function handle(): int
    {
        $this->info('Running checks...');

        $this->runChecks();

        $this->info('All done');

        return self::SUCCESS;
    }

    public function runChecks()
    {
        return OK::configuredChecks()
            ->map(fn($check) => is_string($check) ? app($check) : $check)
            ->map(function (Check $check): Result {
                return $check->shouldRun()
                    ? $this->runCheck($check)
                    : (new Result(Status::SKIPPED))->check($check)->endedAt(now());
            });
    }

    public function runCheck(Check $check)
    {
        $this->output->write("<comment>Running check: {$check->getName()}...</comment> ", false);

        $result = $check();

        $this->outputResultToConsole($result, $exception ?? null);

        return $result;
    }

    protected function outputResultToConsole(Result $result, Exception $exception = null): void
    {
        match ($result->status) {
            Status::OK => $this->output->write('<info>✓ Passed</info>', true),
            Status::FAILED => $this->output->write("<error>✗ Failed: {$result->getMessage()}</error>", true),
            Status::CRASHED => $this->output->write("<error>- Crashed: `{$exception?->getMessage()}`</error>", true),
            default => null,
        };
    }
}
