<?php
// ============================================================================
// File:    Repository.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use Generator;


abstract class Repository
{
    // --------------------------------------------------------------------------
    // CONFIG
    // --------------------------------------------------------------------------

    protected string $table;
    protected string $primaryKey = "id";
    protected string $model;

    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    public function __construct(
        public readonly Database $database
    ) {}

    // --------------------------------------------------------------------------
    // ALL
    // --------------------------------------------------------------------------

    /** @return array<mixed> */
    public function all(): array
    {
        return $this->database
            ->query("
                SELECT *
                FROM {$this->table}
            ")
            ->execute()
            ->fetchAll($this->model);
    }

    public function yieldAll(): Generator
    {
        return $this->database
            ->query("
                SELECT *
                FROM {$this->table}
            ")
            ->execute()
            ->yieldAll($this->model);
    }

    // --------------------------------------------------------------------------
    // BASIC FINDERS
    // --------------------------------------------------------------------------

    public function find(int|string $id): mixed
    {
        return $this->where($this->primaryKey, $id);
    }

    public function exists(int|string $id): bool
    {
        return $this->database
            ->query("
                SELECT 1
                FROM {$this->table}
                WHERE {$this->primaryKey} = :id
                LIMIT 1
            ")
            ->execute(["id" => $id])
            ->exists();
    }

    public function where(string $columnName, int|string $columnValue): mixed
    {
        return $this->database
            ->query("
                SELECT *
                FROM {$this->table}
                WHERE {$columnName} = :value
                LIMIT 1
            ")
            ->execute(["value" => $columnValue])
            ->fetch($this->model);
    }

    // --------------------------------------------------------------------------
    // COUNT
    // --------------------------------------------------------------------------

    public function count(): int
    {
        /** @var int $value */
        $value = $this->database
            ->query("
                SELECT COUNT(*)
                FROM {$this->table}
            ")
            ->execute()
            ->column();
        return $value;
    }

    // --------------------------------------------------------------------------
    // CREATE
    // --------------------------------------------------------------------------

    /** @param array<string, mixed> $data */
    public function create(array $data): string|false
    {
        $columns = implode(
            ", ",
            array_keys($data)
        );

        $placeholders = implode(
            ", ",
            array_map(
                fn(string $key) => ":$key",
                array_keys($data)
            )
        );

        $this->database
            ->query("
                INSERT INTO {$this->table}
                ($columns)
                VALUES ($placeholders)
            ")
            ->execute($data);

        return $this->database->lastInsertId();
    }

    // --------------------------------------------------------------------------
    // UPDATE
    // --------------------------------------------------------------------------

    /** @param array<string, mixed> $data */
    public function update(int|string $id, array $data): bool
    {
        $fields = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }

        $sql = implode(", ", $fields);

        $data["id"] = $id;

        $this->database
            ->query("
                UPDATE {$this->table}
                SET $sql
                WHERE {$this->primaryKey} = :id
            ")
            ->execute($data);

        return $this->database->rowCount() > 0;
    }

    // --------------------------------------------------------------------------
    // DELETE
    // --------------------------------------------------------------------------

    public function delete(int|string $id): bool
    {
        $this->database
            ->query("
                DELETE FROM {$this->table}
                WHERE {$this->primaryKey} = :id
            ")
            ->execute(["id" => $id]);

        return $this->database->rowCount() > 0;
    }
}
