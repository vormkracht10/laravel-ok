<?php

use Illuminate\Support\Facades\File;
use Backstage\Laravel\OK\Checks\Base\Result;
use Backstage\Laravel\OK\Checks\PermissionCheck;
use Backstage\Laravel\OK\Enums\Status;

it('can check permissions for a file', function () {
    $filename = __DIR__.'/'.random_int(0, PHP_INT_MAX).'.txt';

    try {
        $config = [
            'owner' => get_current_user(),
            'group' => posix_getgrgid(posix_getgid())['name'],
            'permissions' => '0644',
        ];

        File::put($filename, '', true);

        $result = (new PermissionCheck([$filename => $config]))->run();

        expect($result)
            ->toBeInstanceOf(Result::class)
            ->status->toBe(Status::OK);
    } finally {
        File::delete($filename);
    }
})->skipOnWindows();

it('can check permissions for a directory', function () {
    $dirname = __DIR__.'/'.random_int(0, PHP_INT_MAX);

    try {
        $config = [
            'owner' => get_current_user(),
            'group' => posix_getgrgid(posix_getgid())['name'],
            'permissions' => '0755',
        ];

        mkdir($dirname);

        $result = (new PermissionCheck([$dirname => $config]))->run();

        expect($result)
            ->toBeInstanceOf(Result::class)
            ->status->toBe(Status::OK);
    } finally {
        rmdir($dirname);
    }
})->skipOnWindows();
