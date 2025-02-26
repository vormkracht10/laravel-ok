<?php

namespace Backstage\Laravel\OK\Checks;

use Illuminate\Support\Facades\DB;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class DatabaseCheck extends Check
{
    protected string $connectionName;

    public function onConnection(string $name): static
    {
        $this->connectionName = $name;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $connectionName = $this->connectionName ?? config('database.default');

        try {
            DB::connection($connectionName)->getPdo();
        } catch (\Throwable) {
            return $result->failed("Could not connect to database on connection [{$connectionName}]");
        }

        return $result->ok('Connected to database successfully');
    }
}
