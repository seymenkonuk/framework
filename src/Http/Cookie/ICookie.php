<?php
// ============================================================================
// File:    ICookie.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Cookie;


interface ICookie
{
    // --------------------------------------------------------------------------
    // IDENTITY
    // --------------------------------------------------------------------------

    /**
     * Cookie'nin adını döndürür.
     *
     * @return string cookie adı.
     */
    public function name(): string;

    /**
     * Cookie değerini döndürür.
     *
     * @return mixed cookie değeri.
     */
    public function value(): mixed;

    // --------------------------------------------------------------------------
    // EXPIRATION
    // --------------------------------------------------------------------------

    /**
     * Cookie'nin sona erme zamanını Unix timestamp olarak döndürür.
     *
     * @return int cookie'nin sona erme zamanı.
     */
    public function expires(): int;

    // --------------------------------------------------------------------------
    // SCOPE
    // --------------------------------------------------------------------------

    /**
     * Cookie'nin geçerli olduğu path bilgisini döndürür.
     *
     * @return string cookie'nin path bilgisi.
     */
    public function path(): string;

    /**
     * Cookie'nin geçerli olduğu domain bilgisini döndürür.
     *
     * @return string cookie'nin domain bilgisi.
     */
    public function domain(): string;

    // --------------------------------------------------------------------------
    // SECURITY
    // --------------------------------------------------------------------------

    /**
     * Cookie'nin yalnızca HTTPS bağlantıları üzerinden gönderilip
     * gönderilmeyeceğini döndürür.
     *
     * @return bool cookie yalnızca HTTPS üzerinden gönderiliyorsa true,
     * aksi halde false.
     */
    public function secure(): bool;

    /**
     * Cookie'nin yalnızca HTTP üzerinden erişilebilir olup olmadığını döndürür.
     *
     * @return bool cookie HTTP üzerinden erişilebiliyorsa true, aksi halde false.
     */
    public function httpOnly(): bool;
}
