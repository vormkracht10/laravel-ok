<?php

use Backstage\Laravel\OK\Checks\Base\Result;
use Backstage\Laravel\OK\Checks\NpmPackageInstalledCheck;
use Backstage\Laravel\OK\Enums\Status;

it('returns ok if all packages are installed', function () {
    $check = new NpmPackageInstalledCheck();
    $check->shouldHave(['lodash', 'axios']);

    $installedPackages = [
        'dependencies' => [
            'lodash' => '1.2.3',
            'axios' => '4.5.6',
        ],
    ];

    $result = $check->with($installedPackages)->run();

    expect($result)->toBeInstanceOf(Result::class)
        ->status->toBe(Status::OK);
});

it('returns failed if some packages are missing', function () {
    $check = new NpmPackageInstalledCheck();
    $check->shouldHave(['lodash', 'axios']);

    $installedPackages = [
        'dependencies' => [
            'lodash' => '1.2.3',
        ],
    ];

    $result = $check->with($installedPackages)->run();

    expect($result)->toBeInstanceOf(Result::class)
        ->status->toBe(Status::FAILED)
        ->getMessage()->toBe('The following packages are missing: axios');
});
