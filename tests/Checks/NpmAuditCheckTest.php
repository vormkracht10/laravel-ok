<?php

use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Checks\NpmAuditCheck;

it('can audit npm packages', function () {
    $npmAuditResult = (new NpmAuditCheck)->run();

    expect($npmAuditResult)->toBeInstanceOf(Result::class);
});
