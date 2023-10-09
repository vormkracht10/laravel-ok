<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class StorageCheck extends Check
{
    protected ?string $path = null;

    protected array $disks = [];

    public function path(string $value = null): static
    {
        $this->path = $value;

        return $this;
    }

    /**
     * @param  array<int, string>|string  $names
     * @return $this
     */
    public function disks($names): static
    {
        $this->disks = Arr::wrap($names);

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $failed = [];

        if (! $this->path) {
            $this->path = 'laravel-ok/storage-check-'.rand();
        }

        $directories = array_filter(
            explode('/', $this->path),
            fn ($s) => ! empty($s),
        );

        array_pop($directories);

        foreach ($this->disks as $disk) {
            $disk = Storage::disk($disk);

            if (! $this->checkDisk($disk, $this->path)) {
                $failed[] = $disk;
            }

            $checked = [];

            foreach ($directories as $directory) {
                $checked[] = $directory;
                $directory = implode('/', $checked);

                if (! empty($disk->allFiles($directory))) {
                    continue;
                }

                $disk->deleteDirectory($directory);
            }
        }

        if (! empty($failed)) {
            return $result->failed('Unable to read or write from some disks: '.implode(', ', $failed));
        }

        return $result->ok('All disks have been verified');
    }

    protected function checkDisk(FilesystemContract $disk, string $path): bool
    {
        $content = rand();

        try {
            $disk->put($path, $content);

            $stored = $disk->get($path);

            return (int) $stored === $content;
        } finally {
            $disk->delete($path);
        }
    }
}
