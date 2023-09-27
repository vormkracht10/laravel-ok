<?php

namespace Vormkracht10\LaravelOK\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class DatabaseSizeCheck extends Check
{
    protected string $connection;

    protected int $max;

    public function onConnection(string $name): static
    {
        $this->connection = $name;

        return $this;
    }

    public function setMaxDatabaseSizeInGigabytes(int $amount): static
    {
        return $this->setMaxDatabaseSizeInMegabytes($amount * 1024);
    }

    public function setMaxDatabaseSizeInMegabytes(int $amount): static
    {
        return $this->setMaxDatabaseSize($amount * 1024 * 1024);
    }

    public function setMaxDatabaseSize(int $bytes): static
    {
        $this->max = $bytes;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $max = $this->max * 1024 * 1024 * 1024;

        $size = $this->getDatabaseSize();

        if ($size > $max) {
            $mb = fn ($bytes) => round($bytes / 1024 / 1024);

            return $result->failed("Database has a size of {$mb($size)}MB, max is configured at {$mb($max)}MB");
        }

        return $result->ok('Database size is under the configured threshold');
    }

    protected function getDatabaseSize(): int
    {
        $connectionName = $this->connection ?? config('database.default');

        $connection = app(ConnectionResolverInterface::class)->connection($connectionName);

        return match (true) {
            $connection instanceof MySqlConnection => $connection->selectOne('SELECT size FROM (SELECT table_schema "name", ROUND(SUM(data_length + index_length)) AS size FROM information_schema.tables GROUP BY table_schema) alias_one WHERE name = ?', [
                $connection->getDatabaseName(),
            ])->size,
            $connection instanceof PostgresConnection => $connection->selectOne('SELECT pg_database_size(?) AS size;', [
                $connection->getDatabaseName(),
            ])->size,
            default => throw new \Exception("Database [{$connection->getDatabaseName()}] not supported"),
        };
    }
}
