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
use Vormkracht10\LaravelOK\OK;

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
        return app(OK::class)
            ->configuredChecks()
            ->map(function (mixed $check) {
                return is_string($check) ? app($check) : $check;
            })
            ->map(function (Check $check): Result {
                return $check->shouldRun()
                    ? $this->runCheck($check)
                    : (new Result(Status::SKIPPED))->check($check)->endedAt(now());
            });
    }

    public function runCheck(Check $check)
    {
        event(new CheckStarted($check));

        try {
            $this->output->write("<comment>Running check: {$check->getName()}...</comment> ", false);

            $result = $check->run();
        } catch (Exception $exception) {
            $exception = CheckDidNotComplete::make($check, $exception);

            report($exception);

            $this->thrownExceptions[] = $exception;

            $result = $check->markAsCrashed();
        }

        $result->check($check)
            ->endedAt(now());

        $this->outputResultToConsole($result, $exception ?? null);

        if (
            $result->status === Status::FAILED ||
            $result->status === Status::CRASHED
        ) {
            event(new CheckFailed($check, $result));
        }

        event(new CheckEnded($check, $result));

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
