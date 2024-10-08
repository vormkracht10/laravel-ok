<?php

namespace Vormkracht10\LaravelOK;

use Vormkracht10\LaravelOK\Facades\OK;
use Spatie\LaravelPackageTools\Package;
use Illuminate\Console\Scheduling\Schedule;
use Vormkracht10\LaravelOK\Events\CheckFailed;
use Vormkracht10\LaravelOK\Interfaces\Scheduled;
use Vormkracht10\LaravelOK\Commands\StatusCommand;
use Vormkracht10\LaravelOK\Jobs\QueueHeartbeatJob;
use Vormkracht10\LaravelOK\Commands\RunChecksCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Vormkracht10\LaravelOK\Commands\SchedulerHeartbeatCommand;
use Vormkracht10\LaravelOK\Listeners\SendCheckFailedNotification;
use Vormkracht10\LaravelOK\Commands\DispatchQueueCheckJobsCommand;

class LaravelOKServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-ok')
            ->hasConfigFile()
            ->hasCommands(
                DispatchQueueCheckJobsCommand::class,
                RunChecksCommand::class,
                SchedulerHeartbeatCommand::class,
                StatusCommand::class,
            )
            ->hasViews('ok')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('vormkracht10/laravel-ok');
            });
    }

    public function bootingPackage()
    {
        $this->callAfterResolving(
            Schedule::class,
            fn(Schedule $schedule) => collect(OK::configuredChecks())
                ->filter(fn($check) => is_a($check, Scheduled::class))
                ->each(fn($check) => $check->schedule($schedule->job($check)))
        );
    }

    public function packageBooted(): void
    {
        $this->silenceQueueHeartbeatJob();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(OK::class);
        $this->app->alias(OK::class, 'ok');

        $this->app['events']->listen(CheckFailed::class, SendCheckFailedNotification::class);
    }

    protected function silenceQueueHeartbeatJob(): static
    {
        $silencedJobs = config('horizon.silenced', []);

        if (in_array(QueueHeartbeatJob::class, $silencedJobs)) {
            return $this;
        }

        $silencedJobs[] = QueueHeartbeatJob::class;

        config()->set('horizon.silenced', $silencedJobs);

        return $this;
    }
}
