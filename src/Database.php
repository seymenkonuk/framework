<?php
// ============================================================================
// File:    Database.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use PDO;
use PDOStatement;

use Throwable;

use Generator;

use Seymenkonuk\Framework\Exception\DatabaseException;


final class Database
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    private ?PDO $pdo = null;
    private ?PDOStatement $statement = null;

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    public function __construct() {}

    public function __destruct()
    {
        $this->pdo = null;
    }

    // --------------------------------------------------------------------------
    // CONNECTION
    // --------------------------------------------------------------------------

    public function connect(
        string $host,
        string $port,
        string $dbname,
        string $charset,
        string $username,
        string $password,
    ): void {
        try {
            $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=$charset", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    // --------------------------------------------------------------------------
    // QUERY
    // --------------------------------------------------------------------------

    public function query(string $sql): self
    {
        try {
            $this->statement = $this->pdo?->prepare($sql) ?: null;
            return $this;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    /** @param array<string, mixed> $params */
    public function execute(array $params = []): self
    {
        try {
            $this->statement?->execute($params);
            return $this;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    // --------------------------------------------------------------------------
    // READ HELPERS
    // --------------------------------------------------------------------------

    public function fetch(?string $class = null): mixed
    {
        try {
            if ($class !== null) {
                $this->statement?->setFetchMode(
                    PDO::FETCH_CLASS,
                    $class,
                );
                return $this->statement?->fetch();
            }

            return $this->statement?->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    /** @return array<mixed> */
    public function fetchAll(?string $class = null): array
    {
        try {
            if ($class !== null) {
                $this->statement?->setFetchMode(
                    PDO::FETCH_CLASS,
                    $class,
                );
                return $this->statement?->fetchAll() ?: [];
            }

            return $this->statement?->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function yieldAll(?string $class = null): Generator
    {
        while (($row = $this->fetch($class)) !== false) {
            yield $row;
        }
    }

    public function column(): mixed
    {
        try {
            return $this->statement?->fetchColumn();
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function exists(): bool
    {
        try {
            return $this->rowCount() > 0;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    // --------------------------------------------------------------------------
    // WRITE HELPERS
    // --------------------------------------------------------------------------

    public function rowCount(): int
    {
        try {
            return $this->statement?->rowCount() ?? 0;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function lastInsertId(): string|false
    {
        try {
            return $this->pdo?->lastInsertId() ?? false;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    // --------------------------------------------------------------------------
    // TRANSACTION
    // --------------------------------------------------------------------------

    public function beginTransaction(): self
    {
        try {
            $this->pdo?->beginTransaction();
            return $this;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function commit(): self
    {
        try {
            $this->pdo?->commit();
            return $this;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function rollBack(): self
    {
        try {
            $this->pdo?->rollBack();
            return $this;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function transaction(callable $callback): mixed
    {
        try {
            $this->beginTransaction();
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollBack();
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }
}
