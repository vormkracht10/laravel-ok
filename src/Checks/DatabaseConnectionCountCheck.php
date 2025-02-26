<?php

namespace Backstage\Laravel\OK\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class DatabaseConnectionCountCheck extends Check
{
    protected ?string $connection = null;

    protected int $maxConnections = 50;

    public function withConnection(string $name): static
    {
        $this->connection = $name;

        return $this;
    }

    public function setMaxConnections($value): static
    {
        $this->maxConnections = $value;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $connection = $this->connection ?? config('database.default');

        if (($count = $this->getConnectionCount($connection)) > $this->maxConnections) {
            return $result->failed("Too many database connections ({$count})");
        }

        return $result->ok("{$count} connections connected to the database");
    }

    protected function getConnectionCount(string $connectionName): int
    {
        $connection = app(ConnectionResolverInterface::class)->connection($connectionName);

        return match (true) {
            $connection instanceof MySqlConnection => (int) $connection->selectOne('show status where variable_name = "threads_connected"')->Value,
            $connection instanceof PostgresConnection => (int) $connection->selectOne('select count(*) as connections from pg_stat_activity')->connections,
            default => throw new \RuntimeException("Connection [{$connectionName}] not supported")
        };
    }
}
