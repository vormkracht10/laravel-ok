<?php

use Illuminate\Support\Facades\Process;
use Vormkracht10\LaravelOK\Checks\Audit\NpmAuditCheck;
use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Enums\Status;

it('passes when there\'s no vulnerabilities', function () {
    $dir = getcwd();

    chdir(__DIR__.'/resources/no-vulnerabilities-npm');

    expect((new NpmAuditCheck)->run())
        ->toBeInstanceOf(Result::class)
        ->status->toBe(Status::OK);

    chdir($dir);
})->skipOnWindows();

it('can use custom data', function () {
    $check = (new NpmAuditCheck)
        ->with(json_decode(<<<'JSON'
            {"auditReportVersion": 2,"vulnerabilities": {"engine.io": {"name": "engine.io","severity": "high","isDirect": false,"via": [{"source": 1089484,"name": "engine.io","dependency": "engine.io","title": "Resource exhaustion in engine.io","url": "https://github.com/advisories/GHSA-j4f2-536g-r55m","severity": "high","cwe": ["CWE-400"],"cvss": {"score": 7.5,"vectorString": "CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:H"},"range": "<3.6.0"},{"source": 1089526,"name": "engine.io","dependency": "engine.io","title": "Uncaught exception in engine.io","url": "https://github.com/advisories/GHSA-r7qp-cfhv-p84w","severity": "moderate","cwe": ["CWE-248"],"cvss": {"score": 6.5,"vectorString": "CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:N/I:N/A:H"},"range": "<3.6.1"}],"effects": ["socket.io"],"range": "<=3.6.0","nodes": ["node_modules/engine.io"],"fixAvailable": true},"socket.io": {"name": "socket.io","severity": "high","isDirect": true,"via": ["engine.io"],"effects": [],"range": "1.0.0-pre - 2.4.1","nodes": ["node_modules/socket.io"],"fixAvailable": true}},"metadata": {"vulnerabilities": {"info": 0,"low": 0,"moderate": 0,"high": 2,"critical": 0,"total": 2},"dependencies": {"prod": 40,"dev": 0,"optional": 0,"peer": 0,"peerOptional": 0,"total": 39}}}
        JSON, true));

    expect($check->run())
        ->toBeInstanceOf(Result::class)
        ->status->toBe(Status::FAILED)
        ->getMessage()->toBe('Found 2 vulnerabilities for your dependencies in NPM');
});

it('fails when dependencies have vulnerabilities', function () {
    $dir = getcwd();

    chdir(__DIR__.'/resources/vulnerabilities-npm');

    $result = (new NpmAuditCheck)->run();

    expect($result)
        ->toBeInstanceOf(Result::class)
        ->status->toBe(Status::FAILED)
        ->getMessage()->toBe('Found 2 vulnerabilities for your dependencies in NPM');

    chdir($dir);
})->skipOnWindows();

it('has access to npm', function () {
    expect(Process::run('npm -v'))
        ->output()->not->toBeEmpty()
        ->exitCode()->toBe(0)
        ->successful()->toBeTrue();
})->skipOnWindows();
