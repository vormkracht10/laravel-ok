<?php

namespace Vormkracht10\LaravelOK;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\LaravelOK\Commands\LaravelOKCommand;

class LaravelOKServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-ok')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-ok_table')
            ->hasCommand(LaravelOKCommand::class);
    }
}
