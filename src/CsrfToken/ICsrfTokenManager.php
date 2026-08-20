<?php
// ============================================================================
// File:    ICsrfTokenManager.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\CsrfToken;


interface ICsrfTokenManager
{
    // --------------------------------------------------------------------------
    // CONSTANTS
    // --------------------------------------------------------------------------

    public const DEFAULT_TTL = 60 * 60;

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    /**
     * Mevcut csrf token değerini döndürür.
     * 
     * Geçerli bir token mevcut değilse false döndürülür.
     * 
     * @return string|false csrf token değeri veya false.
     */
    public function get(): string|false;

    /**
     * Bir csrf token değerinin mevcut olup olmadığını döndürür.
     * 
     * @return bool token mevcutsa true, aksi halde false.
     */
    public function has(): bool;

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen değeri csrf token olarak kaydeder.
     * 
     * $expires değeri saniye cinsindendir ve token değerinin ne kadar süre
     * geçerli kalacağını belirtir.
     * 
     * Mevcut bir token varsa değeri ve geçerlilik süresi güncellenir.
     * 
     * @param string $value kaydedilecek csrf token değeri.
     * @param int $expires token değerinin saniye cinsinden geçerlilik süresi.
     * 
     * @return void
     */
    public function set(string $value, int $expires = self::DEFAULT_TTL): void;

    /**
     * Yeni bir csrf token üretir ve mevcut token değerinin yerine kaydeder.
     * 
     * $expires değeri saniye cinsindendir ve yeni token değerinin ne kadar süre
     * geçerli kalacağını belirtir.
     * 
     * @param int $expires yeni token değerinin saniye cinsinden geçerlilik süresi.
     * 
     * @return string üretilen yeni csrf token değeri.
     */
    public function refresh(int $expires = self::DEFAULT_TTL): string;

    /**
     * Mevcut csrf token değerini geçersiz kılar ve siler.
     * 
     * Token mevcut değilse herhangi bir değişiklik yapılmaz.
     * 
     * @return void
     */
    public function revoke(): void;

    // --------------------------------------------------------------------------
    // VALIDATION
    // --------------------------------------------------------------------------

    /**
     * Mevcut csrf token değerinin süresinin dolup dolmadığını döndürür.
     * 
     * Token mevcut değilse süresi dolmuş kabul edilir.
     * 
     * @return bool token süresi dolmuşsa true, aksi halde false.
     */
    public function expired(): bool;

    /**
     * Belirtilen token değerinin mevcut csrf token ile eşleşip eşleşmediğini
     * döndürür.
     * 
     * Token değerinin geçerli kabul edilebilmesi için mevcut olması, süresinin
     * dolmamış olması ve kaydedilen token değeriyle güvenli biçimde eşleşmesi
     * gerekir.
     * 
     * null verilmesi durumunda false döndürülür.
     * 
     * @param ?string $token doğrulanacak csrf token değeri.
     * 
     * @return bool token geçerliyse true, aksi halde false.
     */
    public function valid(?string $token): bool;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    /**
     * Kullanılan csrf token manager sürücüsünün adını döndürür.
     * 
     * Örneğin: session...
     * 
     * @return string csrf token manager sürücüsünün adı.
     */
    public function driver(): string;
}
