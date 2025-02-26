<?php

namespace Backstage\Laravel\OK\Checks;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class DatabaseTableSizeCheck extends Check
{
    protected string $connectionName;

    protected array $tableSizeThresholds = [];

    public function onConnection(string $name): static
    {
        $this->connectionName = $name;

        return $this;
    }

    public function setMaxTableSizeInGigabytes(array $config): static
    {
        return $this->setMaxTableSizeInMegabytes(
            array_map(fn ($size) => $size * 1000, $config),
        );
    }

    public function setMaxTableSizeInMegabytes(array $config): static
    {
        $this->tableSizeThresholds = $config;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $connectionName = $this->connectionName ?? config('database.default');

        $connection = app(ConnectionResolverInterface::class)->connection($connectionName);

        $config = array_map(
            fn ($MB) => $MB * 1024 * 1024,
            $this->tableSizeThresholds,
        );

        foreach ($config as $table => $max) {
            $size = $this->getTableSize($connection, $table);

            if ($size > $max) {
                $mb = fn ($bytes) => round($bytes / 1024 / 1024, 2);

                return $result->failed("Table [{$table}] size is {$mb($size)} megabytes, max is configured at {$mb($max)} megabytes");
            }
        }

        return $result->ok();
    }

    protected function getTableSize(ConnectionInterface $connection, string $table): int
    {
        return match (true) {
            $connection instanceof MySqlConnection => $connection->selectOne('SELECT (data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ? AND table_name = ?', [
                $connection->getDatabaseName(),
                $table,
            ])->size,
            $connection instanceof PostgresConnection => $connection->selectOne('SELECT pg_total_relation_size(?) AS size;', [
                $table,
            ])->size,
            default => throw new \Exception('This database type is not supported'),
        };
    }
}
