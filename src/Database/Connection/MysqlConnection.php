<?php
// ============================================================================
// File:    MysqlConnection.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Database\Connection;


use PDO;
use PDOStatement;

use Closure;
use Throwable;
use Generator;

use Seymenkonuk\Framework\Exception\DatabaseException;


final class MysqlConnection implements ISqlConnection
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    private PDO $pdo;
    private ?PDOStatement $statement = null;

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    /**
     * Belirtilen bilgilerle veritabanı bağlantısını başlatır.
     *
     * @param string $host veritabanı sunucusunun adresi.
     * @param string $port veritabanı sunucusunun portu.
     * @param string $dbname bağlanılacak veritabanının adı.
     * @param string $charset kullanılacak karakter seti.
     * @param string $username veritabanı kullanıcı adı.
     * @param string $password veritabanı kullanıcı şifresi.
     *
     * @return void
     */
    public function __construct(
        string $host,
        string $port,
        string $dbname,
        string $charset,
        string $username,
        string $password,
    ) {
        try {
            $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=$charset", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    // --------------------------------------------------------------------------
    // STATEMENTS
    // --------------------------------------------------------------------------

    public function query(string $sql): self
    {
        try {
            $this->statement = $this->pdo->prepare($sql);
            return $this;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

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
    // READ
    // --------------------------------------------------------------------------

    public function fetch(?string $class = null): mixed
    {
        try {
            if ($class !== null) {
                $this->statement?->setFetchMode(
                    PDO::FETCH_CLASS,
                    $class,
                );
                /** @phpstan-ignore return.type */
                return $this->statement?->fetch() ?: null;
            }
            /** @phpstan-ignore return.type */
            return $this->statement?->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function all(?string $class = null): array
    {
        try {
            if ($class !== null) {
                $this->statement?->setFetchMode(
                    PDO::FETCH_CLASS,
                    $class,
                );
                /** @phpstan-ignore return.type */
                return $this->statement?->fetchAll() ?: [];
            }

            /** @phpstan-ignore return.type */
            return $this->statement?->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function cursor(?string $class = null): Generator
    {
        while (($row = $this->fetch($class)) !== null) {
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
    // RESULT
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
            return $this->pdo->lastInsertId();
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    // --------------------------------------------------------------------------
    // TRANSACTION
    // --------------------------------------------------------------------------

    public function begin(): self
    {
        try {
            $this->pdo->beginTransaction();
            return $this;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function commit(): self
    {
        try {
            $this->pdo->commit();
            return $this;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function rollback(): self
    {
        try {
            $this->pdo->rollBack();
            return $this;
        } catch (Throwable $e) {
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    public function transaction(Closure $callback): mixed
    {
        try {
            $this->begin();
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollBack();
            throw new DatabaseException($e->getMessage(), previous: $e);
        }
    }

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string
    {
        return "pdo";
    }
}
