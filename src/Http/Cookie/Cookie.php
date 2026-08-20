<?php
// ============================================================================
// File:    Cookie.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Cookie;


final class Cookie
{
    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    /**
     * Yeni bir cookie oluşturur.
     *
     * @param string $name cookie adı.
     * @param mixed $value cookie değeri.
     * @param int $expires cookie'nin sona erme zamanı.
     * @param string $path cookie'nin geçerli olduğu path.
     * @param string $domain cookie'nin geçerli olduğu domain.
     * @param bool $secure cookie'nin yalnızca HTTPS üzerinden gönderilip gönderilmeyeceği.
     * @param bool $httpOnly cookie'nin yalnızca HTTP üzerinden erişilebilir olup olmadığı.
     *
     * @return void
     */
    public function __construct(
        protected string $name,
        protected mixed $value,
        protected int $expires = 0,
        protected string $path = "/",
        protected string $domain = "",
        protected bool $secure = false,
        protected bool $httpOnly = false,
    ) {}

    // --------------------------------------------------------------------------
    // FACTORIES
    // --------------------------------------------------------------------------

    /**
     * Belirtilen cookie'yi silmek için yeni bir cookie oluşturur.
     *
     * Cookie'nin sona erme zamanını geçmiş bir Unix timestamp olarak ayarlar.
     *
     * @param string $name silinecek cookie'nin adı.
     * @param string $path cookie'nin geçerli olduğu path.
     * @param string $domain cookie'nin geçerli olduğu domain.
     *
     * @return static silinmek üzere oluşturulan cookie.
     */
    public static function forget(
        string $name,
        string $path = "/",
        string $domain = "",
    ): static {
        return new Cookie(
            $name,
            "",
            time() - 3600,
            $path,
            $domain,
        );
    }
    
    // --------------------------------------------------------------------------
    // IDENTITY
    // --------------------------------------------------------------------------

    /**
     * Cookie'nin adını döndürür.
     *
     * @return string cookie adı.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Cookie değerini döndürür.
     *
     * @return mixed cookie değeri.
     */
    public function value(): mixed
    {
        return $this->value;
    }

    // --------------------------------------------------------------------------
    // EXPIRATION
    // --------------------------------------------------------------------------

    /**
     * Cookie'nin sona erme zamanını Unix timestamp olarak döndürür.
     *
     * @return int cookie'nin sona erme zamanı.
     */
    public function expires(): int
    {
        return $this->expires;
    }

    // --------------------------------------------------------------------------
    // SCOPE
    // --------------------------------------------------------------------------

    /**
     * Cookie'nin geçerli olduğu path bilgisini döndürür.
     *
     * @return string cookie'nin path bilgisi.
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Cookie'nin geçerli olduğu domain bilgisini döndürür.
     *
     * @return string cookie'nin domain bilgisi.
     */
    public function domain(): string
    {
        return $this->domain;
    }

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
    public function secure(): bool
    {
        return $this->secure;
    }

    /**
     * Cookie'nin yalnızca HTTP üzerinden erişilebilir olup olmadığını döndürür.
     *
     * @return bool cookie HTTP üzerinden erişilebiliyorsa true, aksi halde false.
     */
    public function httpOnly(): bool
    {
        return $this->httpOnly;
    }
}
