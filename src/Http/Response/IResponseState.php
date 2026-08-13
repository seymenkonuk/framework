<?php
// --------------------------------------------------------------------------===================================================
// File:    IResponseState.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// --------------------------------------------------------------------------===================================================

namespace Seymenkonuk\Framework\Http\Response;


use Seymenkonuk\Framework\Http\Cookie\ICookie;


interface IResponseState
{
    // --------------------------------------------------------------------------
    // STATUS CODE
    // --------------------------------------------------------------------------

    /**
     * Response için kullanılan HTTP durum kodunu döndürür.
     *
     * @return int HTTP durum kodu.
     */
    public function status(): int;

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
    public function header(string $key): ?string;

    /**
     * Response'a ait tüm HTTP header değerlerini döndürür.
     *
     * @return array<string, string> header adları ve değerleri.
     */
    public function allHeader(): array;

    /**
     * Belirtilen HTTP header'ın mevcut olup olmadığını döndürür.
     * 
     * @param string $key kontrol edilecek header'ın adı.
     * 
     * @return bool header mevcutsa true, aksi halde false.
     */
    public function hasHeader(string $key): bool;

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
     * @return ?ICookie cookie veya null.
     */
    public function cookie(string $key): ?ICookie;

    /**
     * Response'a ait tüm cookie'leri döndürür.
     *
     * @return array<string, ICookie> cookie adları ve cookie'ler.
     */
    public function allCookie(): array;

    /**
     * Belirtilen cookie'nin mevcut olup olmadığını döndürür.
     * 
     * @param string $key kontrol edilecek cookie'nin adı.
     * 
     * @return bool cookie mevcutsa true, aksi halde false.
     */
    public function hasCookie(string $key): bool;

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
    public function body(): string;

    /**
     * Response olarak gönderilecek dosyanın path bilgisini döndürür.
     *
     * Response olarak bir dosya gönderilmiyorsa null döndürülür.
     *
     * @return ?string dosya path'i veya null.
     */
    public function file(): ?string;
}
