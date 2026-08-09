<?php
// ============================================================================
// File:    ISession.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Session;


interface ISession
{
    // --------------------------------------------------------------------------
    // CONSTANTS
    // --------------------------------------------------------------------------

    /**
     * Normal uygulama verilerinin saklanacağı varsayılan üst anahtar.
     * 
     * Parent key verilmeden yapılan session işlemleri mantıksal olarak bu alan
     * altında gerçekleştirilir.
     * 
     * @var string
     */
    public const DEFAULT_PARENT_KEY = "__data";

    // --------------------------------------------------------------------------
    // SESSION IDENTITY
    // --------------------------------------------------------------------------

    /**
     * Mevcut session kimliğini döndürür.
     * 
     * Aktif bir session kimliği mevcut değilse false döndürülür.
     * 
     * @return string|false session kimliği veya false.
     */
    public function id(): string|false;

    /**
     * Mevcut session kimliğini yeniler.
     * 
     * $deleteOldSession true ise eski session kimliğiyle ilişkili kaydın
     * silinmesi beklenir.
     * 
     * Session verileri yeni kimlik altında korunur.
     * 
     * @param bool $deleteOldSession eski session kaydının silinip silinmeyeceği.
     * 
     * @return bool başarılıysa true, aksi halde false.
     */
    public function regenerate(bool $deleteOldSession = true): bool;

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen üst anahtarın altındaki tüm session verilerini
     * döndürür.
     * 
     * $parentKey verilmezse normal uygulama verilerinin bulunduğu varsayılan
     * alan kullanılır.
     * 
     * Belirtilen alan mevcut değilse boş array döndürülür.
     * 
     * @param string $parentKey okunacak alanın üst anahtarı.
     * 
     * @return array<string, mixed> belirtilen alandaki session verileri.
     */
    public function all(string $parentKey = self::DEFAULT_PARENT_KEY): array;

    /**
     * Belirtilen anahtara ait session değerini döndürür.
     * 
     * $parentKey, $key değerinin bulunduğu üst anahtarı belirtir.
     * 
     * Anahtar mevcut değilse $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param string $key session anahtarı.
     * @param T|null $default anahtar bulunamadığında döndürülecek değer.
     * @param string $parentKey anahtarın bulunduğu üst anahtar.
     * 
     * @return ($default is null ? mixed : T) session değeri veya varsayılan değer.
     */
    public function get(string $key, mixed $default = null, string $parentKey = self::DEFAULT_PARENT_KEY): mixed;

    /**
     * Belirtilen session anahtarının mevcut olup olmadığını döndürür.
     * 
     * $parentKey, $key değerinin bulunduğu üst anahtarı belirtir.
     * 
     * Anahtarın değeri null olsa bile anahtar mevcut kabul edilir.
     * 
     * @param string $key session verilerinde aranacak anahtar.
     * @param string $parentKey anahtarın bulunduğu üst anahtar.
     * 
     * @return bool anahtar mevcutsa true, aksi halde false.
     */
    public function has(string $key, string $parentKey = self::DEFAULT_PARENT_KEY): bool;

    /**
     * Belirtilen anahtara ait session değerini döndürür ve ardından anahtarı
     * session verilerinden siler.
     * 
     * $parentKey, $key değerinin bulunduğu üst anahtarı belirtir.
     * 
     * Anahtar mevcut değilse $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param string $key okunup silinecek session anahtarı.
     * @param T|null $default anahtar bulunamadığında döndürülecek değer.
     * @param string $parentKey anahtarın bulunduğu üst anahtar.
     * 
     * @return ($default is null ? mixed : T) session değeri veya varsayılan değer.
     */
    public function pull(string $key, mixed $default = null, string $parentKey = self::DEFAULT_PARENT_KEY): mixed;

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen üst anahtarın altındaki tüm session verilerini verilen verilerle değiştirir.
     *
     * @param array<string, mixed> $data kaydedilecek session verileri.
     * @param string $parentKey verilerin değiştirileceği üst anahtar.
     *
     * @return bool başarılıysa true, aksi halde false.
     */
    public function replace(array $data, string $parentKey = self::DEFAULT_PARENT_KEY): bool;

    /**
     * Belirtilen değeri session verilerine kaydeder.
     * 
     * $parentKey, $key değerinin bulunduğu üst anahtarı belirtir.
     * 
     * Üst anahtar mevcut değilse oluşturulur. Aynı anahtar mevcutsa değeri
     * güncellenir.
     * 
     * @param string $key session anahtarı.
     * @param mixed $value saklanacak değer.
     * @param string $parentKey anahtarın bulunduğu üst anahtar.
     * 
     * @return bool başarılıysa true, aksi halde false.
     */
    public function set(string $key, mixed $value, string $parentKey = self::DEFAULT_PARENT_KEY): bool;

    /**
     * Belirtilen anahtarı ve ilişkili değeri session verilerinden siler.
     * 
     * $parentKey, $key değerinin bulunduğu üst anahtarı belirtir.
     * 
     * Anahtar mevcut değilse herhangi bir değişiklik yapılmaz.
     * 
     * @param string $key silinecek session anahtarı.
     * @param string $parentKey anahtarın bulunduğu üst anahtar.
     * 
     * @return bool başarılıysa true, aksi halde false.
     */
    public function remove(string $key, string $parentKey = self::DEFAULT_PARENT_KEY): bool;

    /**
     * Belirtilen üst anahtar zincirinin altındaki tüm session verilerini siler.
     * 
     * $parentKey, $key değerinin bulunduğu üst anahtarı belirtir.
     * 
     * Bu işlem session kimliğini veya session'ın tamamını ortadan kaldırmaz.
     * 
     * @param string $parentKey anahtarın bulunduğu üst anahtar.
     * 
     * @return bool başarılıysa true, aksi halde false.
     */
    public function clear(string $parentKey = self::DEFAULT_PARENT_KEY): bool;

    // --------------------------------------------------------------------------
    // DESTROY
    // --------------------------------------------------------------------------

    /**
     * Session'ı tamamen sonlandırır.
     * 
     * Session kimliği ve session ile ilişkili tüm normal ve özel veriler
     * geçersiz hale getirilir.
     * 
     * @return void
     */
    public function destroy(): void;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    /**
     * Kullanılan session sürücüsünün adını döndürür.
     * 
     * Örneğin: redis, filesystem...
     * 
     * @return string session sürücüsünün adı.
     */
    public function driver(): string;
}
