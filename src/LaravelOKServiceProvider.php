<?php

namespace Vormkracht10\LaravelOK;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\LaravelOK\Commands\LaravelOKCommand;

class LaravelOKServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-ok')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_ok_table')
            ->hasCommands(
                LaravelOKCommand::class,
            );
    }
}
