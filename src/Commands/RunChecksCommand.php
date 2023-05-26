<?php

namespace Vormkracht10\LaravelOK\Commands;

use Exception;
use Illuminate\Console\Command;
use Vormkracht10\LaravelOK\Checks\Check;
use Vormkracht10\LaravelOK\Checks\Result;
use Vormkracht10\LaravelOK\Enums\Status;
use Vormkracht10\LaravelOK\Events\CheckEnded;
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

        $this->comment('All done');

        return self::SUCCESS;
    }

    public function runChecks()
    {
        return app(OK::class)
            ->configuredChecks()
            ->dd()
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
            $this->line('');
            $this->line("Running check: {$check->getName()}...");
            $result = $check->run();
        } catch (Exception $exception) {
            $exception = CheckDidNotComplete::make($check, $exception);

            report($exception);

            $this->thrownExceptions[] = $exception;

            $result = $check->markAsCrashed();
        }

        $result->check($check)
            ->endedAt(now());

        dump($this->thrownExceptions);

        $this->outputResultToConsole($result, $exception ?? null);

        event(new CheckEnded($check, $result));

        return $result;
    }

    protected function outputResultToConsole(Result $result, ?Exception $exception = null): void
    {
        match ($result->status) {
            Status::OK => $this->info('Success'),
            Status::FAILED => $this->error("{$result->status}: {$result->getMessage()}"),
            Status::CRASHED => $this->error("{$result->status}}: `{$exception?->getMessage()}`"),
            default => null,
        };
    }
}
