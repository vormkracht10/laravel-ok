<?php

namespace Vormkracht10\LaravelOK;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\LaravelOK\Commands\DispatchQueueCheckJobsCommand;
use Vormkracht10\LaravelOK\Commands\RunChecksCommand;
use Vormkracht10\LaravelOK\Events\CheckFailed;
use Vormkracht10\LaravelOK\Jobs\QueueHeartbeatJob;
use Vormkracht10\LaravelOK\Listeners\SendCheckFailedNotification;

class LaravelOKServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-ok')
            ->hasConfigFile()
            // ->hasMigration('create_laravel_ok_table')
            ->hasCommands(
                RunChecksCommand::class,
                DispatchQueueCheckJobsCommand::class,
            )
            ->hasViews('ok')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    // ->publishMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('vormkracht10/laravel-ok');
            });
    }

    public function packageBooted()
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
