<?php

namespace Vormkracht10\LaravelOK;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\LaravelOK\Commands\RunChecksCommand;
use Vormkracht10\LaravelOK\Commands\SchedulerHeartbeatCommand;
use Vormkracht10\LaravelOK\Events\CheckFailed;
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
                SchedulerHeartbeatCommand::class,
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

    public function packageRegistered(): void
    {
        $this->app->singleton(OK::class);
        $this->app->alias(OK::class, 'ok');

        $this->app['events']->listen(CheckFailed::class, SendCheckFailedNotification::class);
    }
}
