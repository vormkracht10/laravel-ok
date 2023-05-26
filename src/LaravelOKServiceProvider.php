<?php

namespace Vormkracht10\LaravelOK;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\LaravelOK\Commands\RunChecksCommand;

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
            );
    }
}
