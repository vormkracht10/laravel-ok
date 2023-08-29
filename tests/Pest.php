<?php

use Vormkracht10\LaravelOK\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function isWindows(): bool
{
    return strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
}
