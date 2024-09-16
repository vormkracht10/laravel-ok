<?php

namespace Vormkracht10\LaravelOK\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Lorisleiva\CronTranslator\CronTranslator;
use Spatie\Emoji\Emoji;
use Symfony\Component\Console\Helper\TableSeparator;
use Vormkracht10\LaravelOK\Facades\OK;

class StatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ok:status {--F|filter=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show status for all registered checks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $checks = OK::configuredChecks();

        $frequencies = collect(app(Schedule::class)->events())
            ->mapWithKeys(function ($schedule) {
                return [$schedule->description => CronTranslator::translate($schedule->expression)];
            });

        $tableRows = [];

        foreach ($checks as $check) {
            if (
                $this->option('filter') &&
                ! str_contains(strtolower($check->getName()), strtolower($this->option('filter')))
            ) {
                continue;
            }

            $row = [
                Emoji::checkMarkButton(),
                $check->getName(),
                // $check?->updated_at?->diffForHumans() ?: 'N/A',
                $frequencies[$check->getName()] ?? 'N/A',
            ];

            $tableRows[] = $row;
            $tableRows[] = new TableSeparator();
        }

        array_pop($tableRows);

        $this->table(
            [null, 'Check', 'Last Updated', 'Frequency'],
            $tableRows,
            'box',
        );
    }
}
