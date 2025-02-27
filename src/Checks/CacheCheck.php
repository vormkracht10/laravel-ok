<?php

namespace Backstage\Laravel\OK\Checks;

use Exception;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class CacheCheck extends Check
{
    protected array $drivers = [];

    /**
     * @param  array<int, string>  $drivers The drivers that should be checked for read and write access.
     * @return $this
     */
    public function drivers(array $drivers): static
    {
        $this->drivers = $drivers;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        if (empty($this->drivers)) {
            return $result->failed('No configured drivers found for CacheCheck.');
        }

        foreach ($this->drivers as $driver) {
            try {
                if (! $this->checkDriver($driver)) {
                    return $result->failed("Failed to read or delete from or write to driver: {$driver}");
                }
            } catch (InvalidArgumentException) {
                return $result->failed('Oops! Something went wrong, please report this issue on GitHub.');
            }
        }

        return $result->ok('Reading and writing from cache works!');
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function checkDriver(string $driver): bool
    {
        $repository = Cache::driver($driver);

        return $this->checkRead($repository)
            && $this->checkWrite($repository)
            && $this->checkDelete($repository);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function checkRead(Repository $repository): bool
    {
        $key = 'laravel-ok:cache-read-check';

        try {
            return ! $repository->has($key)
                && is_null($repository->get($key));
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Should be run after the checkRead method.
     *
     * @throws InvalidArgumentException
     */
    protected function checkWrite(Repository $repository): bool
    {
        $key = 'laravel-ok:cache-write-check';
        $value = 1;

        try {
            $success = $repository->put(
                $key,
                $value,
                now()->addSeconds(5),
            );

            if (! $success) {
                return $success;
            }

            if (! $repository->has($key)) {
                return false;
            }

            return (int) $repository->get($key) === $value;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Should be run after the checkWrite and checkRead check.
     *
     * @throws InvalidArgumentException
     */
    protected function checkDelete(Repository $repository): bool
    {
        $key = 'laravel-ok:cache-delete-check';

        try {
            $repository->put($key, 'a');

            $repository->forget($key);

            return ! $repository->has($key);
        } catch (Exception) {
            return false;
        }
    }
}
