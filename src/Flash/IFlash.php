<?php
// ============================================================================
// File:    IFlash.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Flash;


interface IFlash
{
    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen anahtara ait flash değerini döndürür.
     * 
     * Yalnızca mevcut istekte okunabilen eski flash verileri kontrol edilir.
     * Anahtar mevcut değilse $default değeri döndürülür.
     * 
     * @param string $key flash anahtarı.
     * @param mixed $default anahtar bulunamadığında döndürülecek değer.
     * 
     * @return mixed flash değeri veya varsayılan değer.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Belirtilen flash anahtarının mevcut olup olmadığını döndürür.
     * 
     * Yalnızca mevcut istekte okunabilen eski flash verileri kontrol edilir.
     * Bir sonraki istek için bekleyen yeni flash verileri dikkate alınmaz.
     * 
     * @param string $key flash'da aranacak anahtar.
     * 
     * @return bool anahtar mevcutsa true, aksi halde false.
     */
    public function has(string $key): bool;

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen değeri bir sonraki istekte kullanılmak üzere flash verilerine
     * kaydeder.
     * 
     * Aynı anahtar yeni flash verilerinde mevcutsa değeri güncellenir.
     * 
     * @param string $key flash anahtarı.
     * @param mixed $value saklanacak değer.
     * 
     * @return self flash nesnesi.
     */
    public function set(string $key, mixed $value): self;

    /**
     * Belirtilen anahtarı ve ilişkili değeri flash verilerinden siler.
     * 
     * Anahtar, hem mevcut istekte okunabilen eski flash verilerinden hem de bir
     * sonraki istek için bekleyen yeni flash verilerinden kaldırılır.
     * 
     * Anahtar mevcut değilse herhangi bir değişiklik yapılmaz.
     * 
     * @param string $key silinecek flash anahtarı.
     * 
     * @return self flash nesnesi.
     */
    public function remove(string $key): self;

    /**
     * Saklanan tüm flash verilerini siler.
     * 
     * Hem mevcut istekte okunabilen eski flash verileri hem de bir sonraki
     * istek için bekleyen yeni flash verileri temizlenir.
     * 
     * @return self flash nesnesi.
     */
    public function clear(): self;

    // --------------------------------------------------------------------------
    // LIFECYCLE
    // --------------------------------------------------------------------------

    /**
     * Flash verilerinin yaşam döngüsünü ilerletir.
     * 
     * Yaşam döngüsü mantıksal olarak şu şekilde ilerler:
     * 
     * 1. Eski flash verileri silinir.
     * 2. Yeni flash verileri eski flash verilerinin yerine taşınır.
     * 3. Yeni flash verileri alanı temizlenir.
     * 
     * Bu metot her istek yaşam döngüsünün başında yalnızca bir kez çağrılmalıdır.
     * 
     * @return self flash nesnesi.
     */
    public function age(): self;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    /**
     * Kullanılan flash sürücüsünün adını döndürür.
     * 
     * Örneğin: session...
     * 
     * @return string flash sürücüsünün adı.
     */
    public function driver(): string;
}
