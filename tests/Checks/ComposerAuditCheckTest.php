<?php

use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Checks\ComposerAuditCheck;

it('can audit composer packages', function () {
    $composerAuditResult = (new ComposerAuditCheck)
        ->run();

    expect($composerAuditResult)->toBeInstanceOf(Result::class);
});
