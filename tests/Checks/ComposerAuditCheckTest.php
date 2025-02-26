<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Backstage\Laravel\OK\Checks\Audit\ComposerAuditCheck;
use Backstage\Laravel\OK\Checks\Base\Result;
use Backstage\Laravel\OK\Enums\Status;

it('can audit composer packages', function () {
    $result = (new ComposerAuditCheck)->run();

    expect($result)->toBeInstanceOf(Result::class);
})->skipOnWindows();

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
        ->getMessage()->toBe('Found 1 vulnerabilities for your dependencies in Composer')
        ->status->toBe(Status::FAILED);
});

it('has access to composer', function () {
    expect(Process::run('composer --version')->successful())
        ->toBeTrue();
})->skipOnWindows();
