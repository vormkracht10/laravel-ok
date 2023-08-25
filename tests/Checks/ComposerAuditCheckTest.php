<?php

use Vormkracht10\LaravelOK\Checks\ComposerAuditCheck;

it('can audit composer packages', function () {
    $composerAuditResult = (new ComposerAuditCheck)
        ->run();

    expect($composerAuditResult)->toBeObject('Vormkracht10\LaravelOK\Checks\Base\Result');
});
