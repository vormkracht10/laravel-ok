<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Enums\SemverLevel;

class ComposerOutdatedCheck extends Check
{
    protected SemverLevel $globalLevel = SemverLevel::All;

    protected Collection $packages;

    protected array $versions;

    public function __construct()
    {
        $this->packages = new Collection;
    }

    public function reportOnlyMajor(): static
    {
        $this->globalLevel = SemverLevel::Major;

        return $this;
    }

    public function reportOnlyMinor(): static
    {
        $this->globalLevel = SemverLevel::Minor;

        return $this;
    }

    /**
     * @param array<string, SemverLevel> $versions
     */
    public function versions(array $versions): static
    {
        $this->versions = $versions;

        return $this;
    }

    /**
     * @param  array<int, string>  $packages
     */
    public function include(array $packages): static
    {
        $packages = collect($packages)->mapWithKeys(
            fn ($package) => [$package => true],
        );

        $this->packages = $this->packages->merge($packages);

        return $this;
    }

    /**
     * @param  array<int, string>  $packages
     */
    public function exclude(array $packages): static
    {
        $packages = collect($packages)->mapWithKeys(
            fn ($package) => [$package => false],
        );

        $this->packages = $this->packages->merge($packages);

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $outdated = $this->getOutdated();

        if (! $outdated) {
            return $result->failed('Failed to get outdated packages using composer');
        }

        $this->packages = (
            ($included = $this->packages->filter(fn ($include) => $include))->isEmpty()
                ? $outdated->mapWithKeys(fn ($versions, $name) => [$name => true])
                : $included
        )->merge($this->packages);

        $failed = collect();

        foreach ($this->packages as $package => $include) {
            if (! $include || ! isset($outdated[$package])) {
                continue;
            }

            $actual = $outdated[$package]['status'];

            $level = $this->versions[$package] ?? $this->globalLevel;

            if ($actual !== $level && $level !== SemverLevel::All) {
                continue;
            }

            $failed[$package] = $outdated[$package];
        }

        if (! $failed->isEmpty()) {
            return $result->failed(<<<string
            \nSome packages are not up-to-date:
            \t{$failed->map(
                fn ($versions, $package) => "{$package}: [current: {$versions['current']}] [latest: {$versions['latest']}]",
            )->implode("\n\t")}
            string);
        }

        return $result->ok('All packages are up-to-date');
    }

    protected function getOutdated(): Collection|false
    {
        $process = Process::run('composer outdated --format=json');

        if (! $process->successful()) {
            return false;
        }

        $data = json_decode(
            $process->output(),
            true,
        );

        return collect($data['installed'])
            ->mapWithKeys(fn ($meta) => [$meta['name'] => [
                'current' => $meta['version'],
                'latest' => $meta['latest'],
                'status' => match ($meta['latest-status']) {
                    'semver-safe-update' => SemverLevel::Minor,
                    'update-possible' => SemverLevel::Major,
                    default => null,
                },
            ]]);
    }
}
