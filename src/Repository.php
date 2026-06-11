<?php
// ============================================================================
// File:    Repository.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


abstract class Repository
{
    // --------------------------------------------------------------------------
    // CONFIG
    // --------------------------------------------------------------------------

    protected string $table;
    protected string $primaryKey = "id";

    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    public function __construct(
        protected Database $db
    ) {}

    // --------------------------------------------------------------------------
    // BASIC FINDERS
    // --------------------------------------------------------------------------

    public function find(int|string $id): mixed
    {
        return $this->db
            ->query("
                SELECT *
                FROM {$this->table}
                WHERE {$this->primaryKey} = :id
                LIMIT 1
            ")
            ->execute(["id" => $id])
            ->fetch();
    }

    /** @return array<mixed> */
    public function all(): array
    {
        return $this->db
            ->query("
                SELECT *
                FROM {$this->table}
            ")
            ->execute()
            ->fetchAll();
    }

    public function exists(int|string $id): bool
    {
        return $this->db
            ->query("
                SELECT 1
                FROM {$this->table}
                WHERE {$this->primaryKey} = :id
                LIMIT 1
            ")
            ->execute(["id" => $id])
            ->exists();
    }

    // --------------------------------------------------------------------------
    // COUNT
    // --------------------------------------------------------------------------

    public function count(): int
    {
        /** @var int $value */
        $value = $this->db
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

        $this->db
            ->query("
                INSERT INTO {$this->table}
                ($columns)
                VALUES ($placeholders)
            ")
            ->execute($data);

        return $this->db->lastInsertId();
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

        $this->db
            ->query("
                UPDATE {$this->table}
                SET $sql
                WHERE {$this->primaryKey} = :id
            ")
            ->execute($data);

        return $this->db->rowCount() > 0;
    }

    // --------------------------------------------------------------------------
    // DELETE
    // --------------------------------------------------------------------------

    public function delete(int|string $id): bool
    {
        $this->db
            ->query("
                DELETE FROM {$this->table}
                WHERE {$this->primaryKey} = :id
            ")
            ->execute(["id" => $id]);

        return $this->db->rowCount() > 0;
    }
}
