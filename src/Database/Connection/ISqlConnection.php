<?php
// ============================================================================
// File:    ISqlConnection.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Database\Connection;


use Generator;

use Seymenkonuk\Framework\Database\Model;


interface ISqlConnection
{
    // --------------------------------------------------------------------------
    // STATEMENTS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen SQL sorgusunu hazırlar.
     *
     * Sorgu hazırlandıktan sonra parametrelerin bağlanması ve sorgunun çalıştırılması
     * için execute() metodu kullanılmalıdır.
     *
     * @param string $sql hazırlanacak SQL sorgusu.
     *
     * @return self
     */
    public function query(string $sql): self;

    /**
     * Hazırlanmış SQL sorgusunu belirtilen parametrelerle çalıştırır.
     *
     * Sorgunun daha önce query() metodu ile hazırlanmış olması gerekir.
     *
     * @param array<string, mixed> $params sorguya bağlanacak parametreler.
     *
     * @return self
     */
    public function execute(array $params = []): self;

    // --------------------------------------------------------------------------
    // READ
    // --------------------------------------------------------------------------

    /**
     * Çalıştırılan sorgudan tek bir kayıt döndürür.
     *
     * $class belirtilirse kayıt belirtilen Model sınıfının örneği olarak döndürülür.
     * Belirtilmezse kayıt anahtar-değer dizisi olarak döndürülür.
     *
     * Kayıt bulunamazsa null döndürülür.
     *
     * @template T of Model
     *
     * @param class-string<T>|null $class kullanılacak Model sınıfı.
     *
     * @return ($class is null ? array<string, mixed>|null : T|null)
     */
    public function fetch(?string $class = null): mixed;

    /**
     * Çalıştırılan sorgudan tüm kayıtları döndürür.
     *
     * $class belirtilirse kayıtlar belirtilen Model sınıfının örnekleri olarak döndürülür. 
     * Belirtilmezse kayıtlar anahtar-değer dizileri olarak döndürülür.
     *
     * @template T of Model
     *
     * @param class-string<T>|null $class kullanılacak Model sınıfı.
     *
     * @return array<int, ($class is null ? array<string, mixed>|null : T|null)>
     */
    public function all(?string $class = null): array;

    /**
     * Çalıştırılan sorgudan kayıtları tek tek döndürür.
     *
     * $class belirtilirse kayıtlar belirtilen Model sınıfının örnekleri olarak döndürülür. 
     * Belirtilmezse kayıtlar anahtar-değer dizileri olarak döndürülür.
     *
     * Kayıtlar belleğe topluca alınmadan üretildiği için büyük sonuç kümelerinde
     * kullanılabilir.
     *
     * @template T of Model
     *
     * @param class-string<T>|null $class kullanılacak Model sınıfı.
     *
     * @return Generator<int, ($class is null ? array<string, mixed>|null : T|null)>
     */
    public function cursor(?string $class = null): Generator;

    /**
     * Çalıştırılan sorgunun ilk kaydındaki ilk sütunu döndürür.
     *
     * Kayıt veya sütun bulunamazsa null döndürülür.
     *
     * @return mixed sorgudan elde edilen sütun değeri.
     */
    public function column(): mixed;

    /**
     * Çalıştırılan sorgunun en az bir kayıt döndürüp döndürmediğini kontrol eder.
     *
     * @return bool sorgu en az bir kayıt döndürüyorsa true, aksi halde false.
     */
    public function exists(): bool;

    // --------------------------------------------------------------------------
    // RESULT
    // --------------------------------------------------------------------------

    /**
     * Son çalıştırılan yazma sorgusunun etkilediği kayıt sayısını döndürür.
     *
     * @return int etkilenen kayıt sayısı.
     */
    public function rowCount(): int;

    /**
     * Son eklenen kaydın kimliğini döndürür.
     *
     * @return string|false son eklenen kaydın kimliği veya kimlik alınamadığında false.
     */
    public function lastInsertId(): string|false;

    // --------------------------------------------------------------------------
    // TRANSACTION
    // --------------------------------------------------------------------------

    /**
     * Yeni bir veritabanı işlemi başlatır.
     *
     * @return self
     */
    public function begin(): self;

    /**
     * Başlatılmış veritabanı işlemini onaylar.
     *
     * @return self
     */
    public function commit(): self;

    /**
     * Başlatılmış veritabanı işlemini geri alır.
     *
     * @return self
     */
    public function rollback(): self;

    /**
     * Belirtilen callback'i bir veritabanı işlemi (transaction) içerisinde çalıştırır.
     *
     * Callback başarıyla tamamlanırsa işlem onaylanır (commit). Callback sırasında bir hata
     * oluşursa işlem geri alınır (rollback) ve hata tekrar fırlatılır.
     *
     * Callback'in döndürdüğü değer aynı şekilde döndürülür.
     *
     * @template T
     *
     * @param callable(ISqlConnection): T $callback veritabanı işlemi içerisinde
     * çalıştırılacak callback.
     *
     * @return T callback tarafından döndürülen değer.
     */
    public function transaction(callable $callback): mixed;
    
    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    /**
     * Kullanılan sql bağlantısı sürücüsünün adını döndürür.
     * 
     * Örneğin: mysql, sqlite, pgsql, mssql...
     * 
     * @return string sql bağlantısı sürücüsünün adı.
     */
    public function driver(): string;
}
