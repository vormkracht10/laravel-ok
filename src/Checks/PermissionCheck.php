<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Facades\File;
use Psy\Util\Json;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class PermissionCheck extends Check
{
    protected array $configured;

    public function __construct(array $configured = [])
    {
        parent::__construct();

        $this->configured = $configured;
    }

    public function run(): Result
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return Result::new()->failed('this check does not run on windows');
        }

        foreach ($this->configured as $file => $configuration) {
            $file = str_starts_with($file, '/') ? $file : base_path($file);

            $actual = collect($this->getMeta($file))->only(array_keys($configuration));

            $diff = array_diff($actual->toArray(), $configuration);

            if (! empty($diff)) {
                $lines = "\n";

                foreach (array_keys($diff) as $name) {
                    $expected = $configuration[$name] ?? 'null';
                    $lines .= "$name: got {$actual[$name]} expected $expected\n";
                }

                return Result::new()->failed(rtrim($lines));
            }
        }

        return Result::new()->ok('file permission expectations were met');
    }

    protected function getMeta(string $file): array
    {
        return [
            'owner' => posix_getpwuid(fileowner($file))['name'],
            'group' => posix_getgrgid(filegroup($file))['name'],
            'permissions' => File::chmod($file),
        ];
    }
}
