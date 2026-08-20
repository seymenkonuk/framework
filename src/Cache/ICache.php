<?php
// ============================================================================
// File:    ICache.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Cache;


use Closure;


interface ICache
{
    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen anahtara ait değeri döndürür.
     * 
     * Anahtar mevcut değilse $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param string $key cache anahtarı.
     * @param T|null $default anahtar bulunamadığında döndürülecek değer.
     * 
     * @return ($default is null ? mixed : T) cache değeri veya varsayılan değer.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Belirtilen anahtarın mevcut olup olmadığını döndürür.
     * 
     * @param string $key cache'de aranacak anahtar.
     * 
     * @return bool anahtar mevcut ise true, aksi halde false.
     */
    public function has(string $key): bool;

    /**
     * Belirtilen anahtara ait değeri döndürür ve ardından anahtarı cache'den siler.
     * 
     * Anahtar mevcut değilse $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param string $key cache anahtarı.
     * @param T|null $default anahtar bulunamadığında döndürülecek değer.
     * 
     * @return ($default is null ? mixed : T) cache değeri veya varsayılan değer.
     */
    public function pull(string $key, mixed $default = null): mixed;

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen değeri cache'e kaydeder.
     * 
     * $ttl değeri saniye cinsindendir. Sıfır verilmesi, değerin süresiz olarak
     * saklanacağını ifade eder.
     * 
     * Aynı anahtar mevcut ise değeri ve yaşam süresi güncellenir.
     * 
     * $keepTtl true ise $ttl değeri görmezden gelinerek mevcut anahtarın 
     * yaşam süresi korunur.
     * 
     * @param string $key cache anahtarı.
     * @param mixed $value saklanacak değer.
     * @param int $ttl değerin saniye cinsinden yaşam süresi.
     * @param bool $keepTtl mevcut yaşam süresinin korunup korunmayacağı.
     * 
     * @return bool değer başarıyla saklandıysa true, aksi halde false.
     */
    public function set(string $key, mixed $value, int $ttl = 0, bool $keepTtl = false): bool;

    /**
     * Belirtilen anahtarı ve ilişkili değeri cache'den siler.
     * 
     * @param string $key silinecek cache anahtarı.
     * 
     * @return bool anahtar başarıyla silindiyse true, aksi halde false.
     */
    public function remove(string $key): bool;

    /**
     * Cache'de saklanan tüm verileri siler.
     * 
     * @return bool başarılıysa true, aksi halde false.
     */
    public function clear(): bool;

    // --------------------------------------------------------------------------
    // INCREMENT / DECREMENT
    // --------------------------------------------------------------------------

    /**
     * Belirtilen anahtara ait sayısal değeri verilen miktarda artırır.
     * 
     * Anahtarın bulunamaması veya ilişkili değerin sayısal olmaması durumundaki
     * davranış beklenmedik olabilir.
     * 
     * @param string $key değeri artırılacak cache anahtarı.
     * @param int $value eklenecek miktar.
     * 
     * @return int artırma işleminden sonraki güncel değer.
     */
    public function increment(string $key, int $value = 1): int;

    /**
     * Belirtilen anahtara ait sayısal değeri verilen miktarda azaltır.
     * 
     * Anahtarın bulunamaması veya ilişkili değerin sayısal olmaması durumundaki
     * davranış beklenmedik olabilir.
     * 
     * @param string $key değer azaltılacak cache anahtarı.
     * @param int $value azaltılacak miktar.
     * 
     * @return int azaltma işleminden sonraki güncel değer.
     */
    public function decrement(string $key, int $value = 1): int;

    // --------------------------------------------------------------------------
    // MULTI
    // --------------------------------------------------------------------------

    /**
     * Belirtilen anahtarlara ait değerleri tek işlemde döndürür.
     * 
     * Mevcut olmayan anahtarlar için $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param array<string> $keys okunacak cache anahtarları.
     * @param T|null $default bulunamayan anahtarlar için kullanılacak değer.
     * 
     * @return array<string, ($default is null ? mixed : T)> anahtarlarla eşleştirilmiş cache değerleri.
     */
    public function getMultiple(array $keys, mixed $default = null): array;

    /**
     * Belirtilen değerleri tek işlemde cache'e kaydeder.
     * 
     * $values dizisinin anahtarları cache anahtarları, değerleri ise saklanılacak
     * veriler olarak kullanılır.
     * 
     * $ttl değeri saniye cinsindendir ve bütün değerler için uygulanır. 
     * Sıfır verilmesi, değerlerin süresiz olarak saklanacağını ifade eder.
     * 
     * $keepTtl true ise $ttl değeri görmezden gelinerek mevcut anahtarların 
     * yaşam süreleri korunur.
     * 
     * Önceden mevcut olan anahtarların değeri ve yaşam süresi güncellenir.
     * 
     * @param array<string, mixed> $values saklanacak anahtarlar ve değerleri.
     * @param int $ttl tüm değerlerin saniye cinsinden saklanma süresi
     * @param bool $keepTtl mevcut anahtarların yaşam sürelerinin korunup korunmayacağı.
     * 
     * @return bool bütün değerler başarıyla saklandıysa true, aksi halde false.
     */
    public function setMultiple(array $values, int $ttl = 0, bool $keepTtl = false): bool;

    /**
     * Belirilen anahtarları ve ilişkili değerleri tek işlemde cache'den siler.
     * 
     * @param array<string> $keys silinecek cache anahtarları
     * 
     * @return bool bütün anahtarlar başarıyla silindiyse true, aksi halde false.
     */
    public function removeMultiple(array $keys): bool;

    // --------------------------------------------------------------------------
    // TTL
    // --------------------------------------------------------------------------

    /**
     * Belirilen anahtarın kalan yaşam süresini saniye cinsinden döndürür.
     * 
     * @param string $key yaşam süresi okunacak cache anahtarı.
     * 
     * @return int anahtarın saniye cinsinden kalan yaşam süresi. 
     */
    public function ttl(string $key): int;

    /**
     * Mevcut bir anahtarın yaşam süresini günceller.
     * 
     * @param string $key yaşam süresi değiştirilecek cache anahtarı.
     * @param int $ttl yeni yaşam süresi.
     * 
     * @return bool başarılıysa true, aksi halde false.
     */
    public function expire(string $key, int $ttl): bool;

    // --------------------------------------------------------------------------
    // LOCK
    // --------------------------------------------------------------------------

    /**
     * Belirtilen anahtar için süreli bir kilit oluşturmaya çalışır.
     * 
     * Anahtar için geçerli kilit zaten bulunuyorsa işlem başarısız olur.
     * 
     * $ttl değeri, kilidin otomatik olarak kaldırılacağı süreyi
     * saniye cinsinden belirtir.
     * 
     * @param string $key kilitlenecek cache anahtarı.
     * @param int $ttl kilidin saniye cinsinden yaşam süresi.
     * 
     * @return bool kilitleme başarılıysa true, aksi halde false.
     */
    public function lock(string $key, int $ttl = 10): bool;

    /**
     * Belirtilen anahtara ait kilidi kaldırır.
     * 
     * @param string $key kilidi kaldırılacak cache anahtarı.
     * 
     * @return bool kilit başarıyla kaldırıldıysa true, aksi halde false.
     */
    public function unlock(string $key): bool;

    /**
     * Belirtilen anahtar için geçerli bir kilit olup olmadığını kontrol eder.
     * 
     * @param string $key kilidi kontrol edilecek cache anahtarı.
     * 
     * @return bool anahtar kilitliyse true, aksi halde false.
     */
    public function isLocked(string $key): bool;

    // --------------------------------------------------------------------------
    // REMEMBER
    // --------------------------------------------------------------------------

    /**
     * Belirtilen anahtara ait değeri cache'den okur.
     * 
     * Anahtar bulunmuyorsa $callback çalıştırılır.
     * Callback tarafından döndürülen değer belirtilen yaşam süresiyle cache'e
     * kaydedilir.
     * 
     * $ttl değeri saniye cinsindendir. Sıfır verilmesi, değerin süresiz olarak
     * saklanacağını ifade eder.
     * 
     * @template T
     * 
     * @param string $key cache anahtarı.
     * @param int $ttl değerin saniye cinsinden yaşam süresi.
     * @param Closure(): T $callback cache'de bulunamazsa getirilecek değer.
     * 
     * @return T cache'deki veya callback tarafından üretilen değer.
     */
    public function remember(string $key, int $ttl, Closure $callback): mixed;

    // --------------------------------------------------------------------------
    // DESTROY
    // --------------------------------------------------------------------------

    /**
     * Cache'i tamamen siler / yok eder.
     * 
     * @return bool başarılıysa true, aksi halde false.
     */
    public function destroy(): bool;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    /**
     * Kullanılan cache sürücüsünün adını döndürür.
     * 
     * Örneğin: redis, filesystem...
     * 
     * @return string cache sürücüsünün adı.
     */
    public function driver(): string;
}
