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
    /**
     * Normal uygulama verilerinin saklanacağı varsayılan üst anahtar zinciri.
     * 
     * Parametre verilmeden yapılan session işlemleri mantıksal olarak bu alan
     * altında gerçekleştirilir.
     * 
     * @var array<string>
     */
    public const DEFAULT_PARENT_KEYS = ["__data"];

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
     * @return self session nesnesi.
     */
    public function regenerate(bool $deleteOldSession = true): self;

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen üst anahtar zincirinin altındaki tüm session verilerini
     * döndürür.
     * 
     * $parentKeys verilmezse normal uygulama verilerinin bulunduğu varsayılan
     * alan kullanılır.
     * 
     * Belirtilen alan mevcut değilse boş array döndürülür.
     * 
     * @param array<string> $parentKeys okunacak alanın üst anahtar zinciri.
     * 
     * @return array<string, mixed> belirtilen alandaki session verileri.
     */
    public function all(array $parentKeys = self::DEFAULT_PARENT_KEYS): array;

    /**
     * Belirtilen anahtara ait session değerini döndürür.
     * 
     * $parentKeys içerisindeki anahtarlar, $key değerinden önce gelen üst
     * anahtarları temsil eder.
     * 
     * Anahtar mevcut değilse $default değeri döndürülür.
     * 
     * @param string $key session anahtarı.
     * @param mixed $default anahtar bulunamadığında döndürülecek değer.
     * @param array<string> $parentKeys anahtarın bulunduğu üst anahtar zinciri.
     * 
     * @return mixed session değeri veya varsayılan değer.
     */
    public function get(string $key, mixed $default = null, array $parentKeys = self::DEFAULT_PARENT_KEYS): mixed;

    /**
     * Belirtilen session anahtarının mevcut olup olmadığını döndürür.
     * 
     * $parentKeys içerisindeki anahtarlar, $key değerinden önce gelen üst
     * anahtarları temsil eder.
     * 
     * Anahtarın değeri null olsa bile anahtar mevcut kabul edilir.
     * 
     * @param string $key session verilerinde aranacak anahtar.
     * @param array<string> $parentKeys anahtarın bulunduğu üst anahtar zinciri.
     * 
     * @return bool anahtar mevcutsa true, aksi halde false.
     */
    public function has(string $key, array $parentKeys = self::DEFAULT_PARENT_KEYS): bool;

    /**
     * Belirtilen anahtara ait session değerini döndürür ve ardından anahtarı
     * session verilerinden siler.
     * 
     * $parentKeys içerisindeki anahtarlar, $key değerinden önce gelen üst
     * anahtarları temsil eder.
     * 
     * Anahtar mevcut değilse $default değeri döndürülür.
     * 
     * @param string $key okunup silinecek session anahtarı.
     * @param mixed $default anahtar bulunamadığında döndürülecek değer.
     * @param array<string> $parentKeys anahtarın bulunduğu üst anahtar zinciri.
     * 
     * @return mixed session değeri veya varsayılan değer.
     */
    public function pull(string $key, mixed $default = null, array $parentKeys = self::DEFAULT_PARENT_KEYS): mixed;

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen değeri session verilerine kaydeder.
     * 
     * $parentKeys içerisindeki anahtarlar, $key değerinden önce gelen üst
     * anahtarları temsil eder.
     * 
     * Üst anahtarlar mevcut değilse oluşturulur. Aynı anahtar mevcutsa değeri
     * güncellenir.
     * 
     * @param string $key session anahtarı.
     * @param mixed $value saklanacak değer.
     * @param array<string> $parentKeys değerin yazılacağı üst anahtar zinciri.
     * 
     * @return self session nesnesi.
     */
    public function set(string $key, mixed $value, array $parentKeys = self::DEFAULT_PARENT_KEYS): self;

    /**
     * Belirtilen anahtarı ve ilişkili değeri session verilerinden siler.
     * 
     * $parentKeys içerisindeki anahtarlar, $key değerinden önce gelen üst
     * anahtarları temsil eder.
     * 
     * Anahtar mevcut değilse herhangi bir değişiklik yapılmaz.
     * 
     * @param string $key silinecek session anahtarı.
     * @param array<string> $parentKeys anahtarın bulunduğu üst anahtar zinciri.
     * 
     * @return self session nesnesi.
     */
    public function remove(string $key, array $parentKeys = self::DEFAULT_PARENT_KEYS): self;

    /**
     * Belirtilen üst anahtar zincirinin altındaki tüm session verilerini siler.
     * 
     * $parentKeys verilmezse yalnızca normal uygulama verilerinin bulunduğu
     * varsayılan alan temizlenir. Diğer özel session alanları korunur.
     * 
     * Bu işlem session kimliğini veya session'ın tamamını ortadan kaldırmaz.
     * 
     * @param array<string> $parentKeys temizlenecek alanın üst anahtar zinciri.
     * 
     * @return self session nesnesi.
     */
    public function clear(array $parentKeys = self::DEFAULT_PARENT_KEYS): self;

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
