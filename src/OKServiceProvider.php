<?php

namespace Backstage\Laravel\OK;

use Backstage\Laravel\OK\Facades\OK;
use Spatie\LaravelPackageTools\Package;
use Illuminate\Console\Scheduling\Schedule;
use Backstage\Laravel\OK\Events\CheckFailed;
use Backstage\Laravel\OK\Interfaces\Scheduled;
use Backstage\Laravel\OK\Commands\StatusCommand;
use Backstage\Laravel\OK\Jobs\QueueHeartbeatJob;
use Backstage\Laravel\OK\Commands\RunChecksCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Backstage\Laravel\OK\Commands\SchedulerHeartbeatCommand;
use Backstage\Laravel\OK\Listeners\SendCheckFailedNotification;
use Backstage\Laravel\OK\Commands\DispatchQueueCheckJobsCommand;

class OKServiceProvider extends PackageServiceProvider
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
                    ->askToStarRepoOnGitHub('backstagephp/laravel-ok');
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
