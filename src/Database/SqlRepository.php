<?php
// ============================================================================
// File:    SqlRepository.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Database;


use Generator;

use Seymenkonuk\Framework\Database\Connection\ISqlConnection;


/** @template T of Model */
abstract class SqlRepository
{
    // --------------------------------------------------------------------------
    // CONFIG
    // --------------------------------------------------------------------------

    /**
     * Repository tarafından kullanılacak tablo adı.
     *
     * @var string
     */
    protected string $table;

    /**
     * Tablonun primary key sütununun adı.
     *
     * @var string
     */
    protected string $primaryKey = "id";

    /**
     * Veritabanı kayıtlarının dönüştürüleceği model sınıfı.
     *
     * @var class-string<T>
     */
    protected string $model;

    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    /**
     * Repository için gerekli veritabanı bağlantısını oluşturur.
     *
     * @param ISqlConnection $database kullanılacak veritabanı bağlantısı.
     */
    public function __construct(
        public readonly ISqlConnection $database
    ) {}

    // --------------------------------------------------------------------------
    // ALL
    // --------------------------------------------------------------------------

    /**
     * Tablodaki tüm kayıtları döndürür.
     *
     * Her kayıt repository tarafından belirtilen model sınıfına dönüştürülür.
     *
     * Kayıt bulunmaması durumunda boş array döndürülür.
     *
     * @return array<int, T> tablodaki kayıtlar.
     */
    public function all(): mixed
    {
        return $this->database
            ->query("
                SELECT *
                FROM {$this->table}
            ")
            ->execute()
            ->all($this->model);
    }

    /**
     * Tablodaki kayıtları tek tek üretir.
     *
     * Kayıtlar belleğe topluca alınmadan cursor üzerinden okunur.
     * Her kayıt repository tarafından belirtilen model sınıfına dönüştürülür.
     *
     * Kayıt bulunmaması durumunda boş bir generator döndürülür.
     *
     * @return Generator<int, T> tablodaki kayıtları üreten generator.
     */
    public function yieldAll(): Generator
    {
        return $this->database
            ->query("
                SELECT *
                FROM {$this->table}
            ")
            ->execute()
            ->cursor($this->model);
    }

    // --------------------------------------------------------------------------
    // BASIC FINDERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen primary key değerine ait kaydı döndürür.
     *
     * Kayıt bulunamazsa null döndürülür.
     *
     * @param int|string $id aranacak kaydın primary key değeri.
     *
     * @return T|null bulunan satır veya null.
     */
    public function find(int|string $id): mixed
    {
        return $this->where($this->primaryKey, $id);
    }

    /**
     * Belirtilen primary key değerine ait bir kaydın mevcut olup olmadığını
     * döndürür.
     *
     * @param int|string $id kontrol edilecek kaydın primary key değeri.
     *
     * @return bool kayıt mevcutsa true, aksi halde false.
     */
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

    /**
     * Belirtilen sütun değerine ait ilk kaydı döndürür.
     *
     * Kayıt bulunamazsa null döndürülür.
     *
     * @param string $columnName aranacak sütunun adı.
     * @param int|string $columnValue aranacak sütun değeri.
     *
     * @return T|null bulunan satır veya null.
     */
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

    /**
     * Tablodaki toplam kayıt sayısını döndürür.
     *
     * @return int tablodaki kayıt sayısı.
     */
    public function count(): int
    {
        /** @var int */
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

    /**
     * Belirtilen verilerle yeni bir kayıt oluşturur.
     *
     * Dizinin anahtarları sütun adları, değerleri ise kaydedilecek değerler
     * olarak kullanılır.
     *
     * Kayıt başarıyla oluşturulduğunda oluşturulan kaydın primary key değeri
     * döndürülür.
     *
     * @param array<string, mixed> $data kaydedilecek sütunlar ve değerleri.
     *
     * @return string|false kayıt başarıyla oluşturulduysa primary key değeri,
     * aksi halde false.
     */
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

    /**
     * Belirtilen kaydın değerlerini günceller.
     *
     * Dizinin anahtarları güncellenecek sütun adları, değerleri ise yeni
     * sütun değerleri olarak kullanılır.
     *
     * Belirtilen kayıt mevcut değilse veya herhangi bir kayıt güncellenmezse
     * false döndürülür.
     *
     * @param int|string $id güncellenecek kaydın primary key değeri.
     * @param array<string, mixed> $data güncellenecek sütunlar ve değerleri.
     *
     * @return bool kayıt başarıyla güncellendiyse true, aksi halde false.
     */
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

    /**
     * Belirtilen kaydı siler.
     *
     * Kayıt mevcut değilse veya herhangi bir kayıt silinmezse false döndürülür.
     *
     * @param int|string $id silinecek kaydın primary key değeri.
     *
     * @return bool kayıt başarıyla silindiyse true, aksi halde false.
     */
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
