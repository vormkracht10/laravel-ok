<?php

use Illuminate\Support\Facades\Http;
use Vormkracht10\LaravelOK\Checks\Audit\ComposerAuditCheck;
use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Enums\Status;

it('can audit composer packages', function () {
    $result = (new ComposerAuditCheck)->run();

    expect($result)->toBeInstanceOf(Result::class);
});

it('can use custom data', function () {
    $result = (new ComposerAuditCheck)
        ->with(['advisories' => []])
        ->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toBe(Status::OK);
});

it('fails when dependencies have vulnerabilities', function () {
    // URL is an example from the official composer documentation.
    $response = Http::asJson()->get('https://packagist.org/api/security-advisories/?packages[]=monolog/monolog');

    expect($response)
        ->ok()->toBeTrue();

    $data = $response->json();

    $result = (new ComposerAuditCheck)
        ->with($data)
        ->run();

    expect($result)
        ->getMessage()->toBe('Found 1 vulnerabilities for your dependencies in Composer.')
        ->status->toBe(Status::FAILED);
});

