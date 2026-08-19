<?php
// --------------------------------------------------------------------------===================================================
// File:    ResponseState.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// --------------------------------------------------------------------------===================================================

namespace Seymenkonuk\Framework\Http\Response;


use Seymenkonuk\Framework\Http\Cookie\Cookie;


final class ResponseState
{

    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    /**
     * Yeni bir response state'i oluşturur.
     *
     * Response'un durum kodu, header'ları, cookie'leri ve gövde bilgilerini saklar.
     *
     * @param int $status HTTP durum kodu.
     * @param array<string, string> $headers HTTP header adları ve değerleri.
     * @param array<string, Cookie> $cookies cookie adları ve cookie'ler.
     * @param string $body response body içeriği.
     * @param ?string $file response olarak gönderilecek dosyanın path'i.
     *
     * @return void
     */
    public function __construct(
        protected int $status,
        protected array $headers,
        protected array $cookies,
        protected string $body,
        protected ?string $file = null,
    ) {}

    // --------------------------------------------------------------------------
    // STATUS CODE
    // --------------------------------------------------------------------------

    /**
     * Response için kullanılan HTTP durum kodunu döndürür.
     *
     * @return int HTTP durum kodu.
     */
    public function status(): int
    {
        return $this->status;
    }

    // --------------------------------------------------------------------------
    //  HEADERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen HTTP header değerini döndürür.
     *
     * Header mevcut değilse null döndürülür.
     *
     * @param string $key okunacak header'ın adı.
     *
     * @return ?string header değeri veya null.
     */
    public function header(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * Response'a ait tüm HTTP header değerlerini döndürür.
     *
     * @return array<string, string> header adları ve değerleri.
     */
    public function allHeader(): array
    {
        return $this->headers;
    }

    /**
     * Belirtilen HTTP header'ın mevcut olup olmadığını döndürür.
     * 
     * @param string $key kontrol edilecek header'ın adı.
     * 
     * @return bool header mevcutsa true, aksi halde false.
     */
    public function hasHeader(string $key): bool
    {
        return array_key_exists($key, $this->headers);
    }

    // --------------------------------------------------------------------------
    //  COOKIES
    // --------------------------------------------------------------------------

    /**
     * Belirtilen cookie'yi döndürür.
     *
     * Cookie mevcut değilse null döndürülür.
     *
     * @param string $key okunacak cookie'nin adı.
     *
     * @return ?Cookie cookie veya null.
     */
    public function cookie(string $key): ?Cookie
    {
        return $this->cookies[$key] ?? null;
    }

    /**
     * Response'a ait tüm cookie'leri döndürür.
     *
     * @return array<string, Cookie> cookie adları ve cookie'ler.
     */
    public function allCookie(): array
    {
        return $this->cookies;
    }

    /**
     * Belirtilen cookie'nin mevcut olup olmadığını döndürür.
     * 
     * @param string $key kontrol edilecek cookie'nin adı.
     * 
     * @return bool cookie mevcutsa true, aksi halde false.
     */
    public function hasCookie(string $key): bool
    {
        return array_key_exists($key, $this->cookies);
    }

    // --------------------------------------------------------------------------
    //  BODY
    // --------------------------------------------------------------------------

    /**
     * Response gövdesinin içeriğini döndürür.
     * 
     * Response olarak bir dosya gönderiliyorsa boş string döndürür.
     *
     * @return string response body içeriği.
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Response olarak gönderilecek dosyanın path bilgisini döndürür.
     *
     * Response olarak bir dosya gönderilmiyorsa null döndürülür.
     *
     * @return ?string dosya path'i veya null.
     */
    public function file(): ?string
    {
        return $this->file;
    }
}
