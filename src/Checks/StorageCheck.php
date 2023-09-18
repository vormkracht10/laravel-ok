<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class StorageCheck extends Check
{
    protected string $filename = 'laravel-ok::storage-check::file';

    protected array $disks = [];

    public function filename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @param  array<int, string>|string  $names
     * @return $this
     */
    public function disks($names): static
    {
        $names = Arr::wrap($names);

        $this->disks = $names;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $failed = [];

        foreach ($this->disks as $disk) {
            $content = rand();

            try {
                Storage::disk($disk)->put($this->filename, $content);

                $stored = Storage::disk($disk)->get($this->filename);
            } finally {
                Storage::disk($disk)->delete($this->filename);
            }

            if ((int) $stored !== $content) {
                $failed[] = $disk;
            }
        }

        if (! empty($failed)) {
            return $result->failed('Unable to read or write from some disks: '.implode(', ', $failed));
        }

        return $result->ok('All disks have been verified');
    }
}
