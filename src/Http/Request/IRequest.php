<?php
// ============================================================================
// File:    IRequest.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Request;


use Seymenkonuk\Framework\Http\UploadedFile\IUploadedFile;


interface IRequest
{
    // --------------------------------------------------------------------------
    //  HTTP INFO
    // --------------------------------------------------------------------------

    /**
     * İstek için kullanılan HTTP metodunu döndürür.
     * 
     * @return string HTTP metodunun adı.
     */
    public function method(): string;

    /**
     * İstek için kullanılan HTTP protokol sürümünü döndürür.
     * 
     * Örneğin: HTTP/1.1, HTTP/2, HTTP/3...
     * 
     * @return string HTTP protokol sürümünün adı.
     */
    public function version(): string;

    /**
     * İsteğin path bilgisini döndürür.
     * 
     * Query string ve diğer URL bileşenleri dahil edilmez.
     * 
     * @return string isteğin path bilgisi.
     */
    public function path(): string;

    /**
     * İsteğin tam URL bilgisini döndürür.
     * 
     * @return string isteğin tam URL bilgisi.
     */
    public function url(): string;

    /**
     * İsteği gönderen istemcinin IP adresini döndürür.
     * 
     * @return string istemcinin IP adresi.
     */
    public function ip(): string;

    /**
     * İsteği gönderen istemcinin user agent bilgisini döndürür.
     * 
     * User agent bilgisi mevcut değilse null döndürülür.
     * 
     * @return ?string user agent bilgisi veya null.
     */
    public function userAgent(): ?string;

    // --------------------------------------------------------------------------
    //  HEADERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen HTTP header değerini döndürür.
     * 
     * Header mevcut değilse $default değeri döndürülür.
     * 
     * @param string $key okunacak header'ın adı.
     * @param string $default header mevcut olmadığında döndürülecek değer.
     * 
     * @return string header değeri veya varsayılan değer.
     */
    public function header(string $key, string $default = ""): string;

    /**
     * İsteğe ait tüm HTTP header değerlerini döndürür.
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
     * Belirtilen cookie değerini döndürür.
     * 
     * Cookie mevcut değilse $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param string $key okunacak cookie'nin adı.
     * @param T|null $default cookie mevcut olmadığında döndürülecek değer.
     * 
     * @return ($default is null ? mixed : T) cookie değeri veya varsayılan değer.
     */
    public function cookie(string $key, mixed $default = null): mixed;

    /**
     * İsteğe ait tüm cookie değerlerini döndürür.
     * 
     * @return array<string, mixed> cookie adları ve değerleri.
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
    //  ALL
    // --------------------------------------------------------------------------

    /**
     * İsteğe ait kullanıcı tarafından gönderilen tüm verileri döndürür.
     * 
     * Döndürülen dizi body, query, params ve files alanlarını içerir.
     * 
     * @return array{
     *     body: array<string, mixed>,
     *     query: array<string, mixed>,
     *     params: array<string, mixed>,
     *     files: array<string, IUploadedFile>
     * } istek verileri.
     */
    public function all(): array;

    // --------------------------------------------------------------------------
    //  BODY
    // --------------------------------------------------------------------------

    /**
     * İstek gövdesinin ham içeriğini döndürür.
     * 
     * @return string isteğin ham body içeriği.
     */
    public function body(): string;

    // --------------------------------------------------------------------------
    //  POST
    // --------------------------------------------------------------------------

    /**
     * Belirtilen POST verisini döndürür.
     * 
     * POST verisi mevcut değilse $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param string $key okunacak POST verisinin anahtarı.
     * @param T|null $default veri mevcut olmadığında döndürülecek değer.
     * 
     * @return ($default is null ? mixed : T) POST değeri veya varsayılan değer.
     */
    public function post(string $key, mixed $default = null): mixed;

    /**
     * İsteğe ait tüm POST verilerini döndürür.
     * 
     * @return array<string, mixed> POST verileri.
     */
    public function allPost(): array;

    /**
     * Belirtilen POST verisinin mevcut olup olmadığını döndürür.
     *  
     * @param string $key kontrol edilecek POST verisinin anahtarı.
     * 
     * @return bool POST verisi mevcutsa true, aksi halde false.
     */
    public function hasPost(string $key): bool;

    // --------------------------------------------------------------------------
    //  JSON
    // --------------------------------------------------------------------------

    /**
     * Belirtilen JSON verisini döndürür.
     * 
     * JSON gövdesi çözümlenemiyorsa veya belirtilen anahtar mevcut değilse
     * $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param string $key okunacak JSON verisinin anahtarı.
     * @param T|null $default veri mevcut olmadığında döndürülecek değer.
     * 
     * @return ($default is null ? mixed : T) JSON değeri veya varsayılan değer.
     */
    public function json(string $key, mixed $default = null): mixed;

    /**
     * İsteğe ait tüm JSON verilerini döndürür.
     * 
     * JSON gövdesi çözümlenemiyorsa boş array döndürülür.
     * 
     * @return array<string, mixed> çözümlenen JSON verileri.
     */
    public function allJson(): array;

    /**
     * Belirtilen JSON verisinin mevcut olup olmadığını döndürür.
     * 
     * JSON gövdesi çözümlenemiyorsa veya belirtilen anahtar mevcut değilse
     * false döndürülür.
     * 
     * @param string $key kontrol edilecek JSON verisinin anahtarı.
     * 
     * @return bool JSON verisi mevcutsa true, aksi halde false.
     */
    public function hasJson(string $key): bool;

    // --------------------------------------------------------------------------
    //  QUERY
    // --------------------------------------------------------------------------

    /**
     * Belirtilen query verisini döndürür.
     * 
     * Query verisi mevcut değilse $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param string $key okunacak query verisinin anahtarı.
     * @param T|null $default veri mevcut olmadığında döndürülecek değer.
     * 
     * @return ($default is null ? mixed : T) query değeri veya varsayılan değer.
     */
    public function query(string $key, mixed $default = null): mixed;

    /**
     * İsteğe ait tüm query verilerini döndürür.
     * 
     * @return array<string, mixed> query verileri.
     */
    public function allQuery(): array;

    /**
     * Belirtilen query verisinin mevcut olup olmadığını döndürür.
     * 
     * @param string $key kontrol edilecek query verisinin anahtarı.
     * 
     * @return bool query verisi mevcutsa true, aksi halde false.
     */
    public function hasQuery(string $key): bool;

    // --------------------------------------------------------------------------
    //  ROUTE PARAMETERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen route parametresinin değerini döndürür.
     * 
     * Route parametresi mevcut değilse $default değeri döndürülür.
     * 
     * @template T
     * 
     * @param string $key okunacak route parametresinin adı.
     * @param T|null $default parametre mevcut olmadığında döndürülecek değer.
     * 
     * @return ($default is null ? mixed : T) route parametresi veya varsayılan değer.
     */
    public function route(string $key, mixed $default = null): mixed;

    /**
     * İsteğe ait tüm route parametrelerini döndürür.
     * 
     * @return array<string, mixed> route parametreleri.
     */
    public function allRoute(): array;

    /**
     * Belirtilen route parametresinin mevcut olup olmadığını döndürür.
     * 
     * @param string $key kontrol edilecek route parametresinin adı.
     * 
     * @return bool route parametresi mevcutsa true, aksi halde false.
     */
    public function hasRoute(string $key): bool;

    // --------------------------------------------------------------------------
    //  FILES
    // --------------------------------------------------------------------------

    /**
     * Belirtilen yüklenen dosyayı döndürür.
     * 
     * Dosya mevcut değilse $default değeri döndürülür.
     * 
     * @param string $key okunacak dosyanın anahtarı.
     * @param ?IUploadedFile $default dosya mevcut olmadığında döndürülecek değer.
     * 
     * @return ?IUploadedFile yüklenen dosya veya varsayılan değer.
     */
    public function file(string $key, ?IUploadedFile $default = null): ?IUploadedFile;

    /**
     * İstekteki tüm yüklenen dosyaları döndürür.
     * 
     * @return array<string, IUploadedFile> yüklenen dosyalar.
     */
    public function allFiles(): array;

    /**
     * Belirtilen dosyanın mevcut olup olmadığını döndürür.
     * 
     * @param string $key kontrol edilecek dosyanın anahtarı.
     * 
     * @return bool dosya mevcutsa true, aksi halde false.
     */
    public function hasFile(string $key): bool;

    // --------------------------------------------------------------------------
    //  HELPERS
    // --------------------------------------------------------------------------

    /**
     * İsteğin AJAX isteği olup olmadığını döndürür.
     * 
     * @return bool istek AJAX isteğiyse true, aksi halde false.
     */
    public function isAjax(): bool;

    /**
     * İsteğin JSON içerik türüne sahip olup olmadığını döndürür.
     * 
     * @return bool istek JSON içerik türüne sahipse true, aksi halde false.
     */
    public function isJson(): bool;

    /**
     * İstemcinin JSON yanıtı bekleyip beklemediğini döndürür.
     * 
     * @return bool istemci JSON yanıtı bekliyorsa true, aksi halde false.
     */
    public function expectsJson(): bool;

    // --------------------------------------------------------------------------
    // DERIVATION
    // --------------------------------------------------------------------------

    /**
     * İsteğin belirtilen veriler eklenmiş yeni bir kopyasını oluşturur.
     *
     * Mevcut istek değiştirilmez.
     * Belirtilen anahtarlar mevcut verilerle aynıysa yeni değerler kullanılır.
     *
     * @param array<string, mixed> $data yeni isteğe eklenecek veya mevcut değerlerin
     * üzerine yazılacak veriler.
     *
     * @return IRequest belirtilen veriler eklenmiş yeni istek.
     */
    public function with(array $data): IRequest;
}
