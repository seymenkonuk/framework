<?php
// ============================================================================
// File:    IResponse.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Response;


use Seymenkonuk\Framework\Http\Cookie\Cookie;


interface IResponse
{
    // --------------------------------------------------------------------------
    // VIEW RENDERING
    // --------------------------------------------------------------------------

    /**
     * Belirtilen view'ı verilen verilerle oluşturur.
     *
     * @param string $viewName oluşturulacak view'ın adı.
     * @param array<string, mixed> $data view'a aktarılacak veriler.
     *
     * @return self
     */
    public function view(string $viewName, array $data = []): self;

    /**
     * Belirtilen component'i verilen verilerle oluşturur.
     *
     * @param string $componentName oluşturulacak component'in adı.
     * @param array<string, mixed> $data component'e aktarılacak veriler.
     *
     * @return self
     */
    public function component(string $componentName, array $data = []): self;

    // --------------------------------------------------------------------------
    // CONTENT
    // --------------------------------------------------------------------------

    /**
     * Belirtilen içeriği response gövdesi olarak ayarlar.
     *
     * $contentType belirtilen içeriğin MIME türünü belirtir.
     *
     * @param string $content response gövdesi olarak kullanılacak içerik.
     * @param string $contentType içeriğin MIME türü.
     *
     * @return self response nesnesi.
     */
    public function content(string $content, string $contentType): self;

    /**
     * Belirtilen veriyi JSON response olarak oluşturur.
     *
     * @param array<mixed, mixed> $data JSON olarak dönüştürülecek veri.
     *
     * @return self
     */
    public function json(array $data): self;

    /**
     * Belirtilen içeriği HTML response olarak oluşturur.
     *
     * @param string $content response olarak gönderilecek HTML içeriği.
     *
     * @return self
     */
    public function html(string $content): self;

    /**
     * Belirtilen içeriği text response olarak oluşturur.
     *
     * @param string $content response olarak gönderilecek metin.
     *
     * @return self
     */
    public function text(string $content): self;

    // --------------------------------------------------------------------------
    // STATUS
    // --------------------------------------------------------------------------

    /**
     * Response'un HTTP durum kodunu günceller.
     *
     * @param int $code kullanılacak HTTP durum kodu.
     *
     * @return self
     */
    public function status(int $code): self;

    /**
     * Belirtilen HTTP durum koduyla bir hata response'u oluşturur.
     *
     * Response içeriği hata view'ı kullanılarak oluşturulur.
     *
     * @param int $code kullanılacak HTTP hata durum kodu.
     * @param array<string, mixed> $data hata view'ına aktarılacak veriler.
     *
     * @return self
     */
    public function abort(int $code, array $data = []): self;

    // --------------------------------------------------------------------------
    // REDIRECT
    // --------------------------------------------------------------------------

    /**
     * Belirtilen URL'ye yönlendiren response oluşturur.
     *
     * @param string $url yönlendirilecek URL.
     *
     * @return self
     */
    public function redirect(string $url): self;

    // --------------------------------------------------------------------------
    // FILE RESPONSES
    // --------------------------------------------------------------------------

    /**
     * Belirtilen dosyayı response olarak gönderir.
     *
     * $contentType verilmezse dosyanın gerçek içerik türü kullanılır.
     *
     * @param string $path gönderilecek dosyanın path'i.
     * @param ?string $contentType dosyanın içerik türü.
     *
     * @return self
     */
    public function file(string $path, ?string $contentType = null): self;

    /**
     * Belirtilen dosyayı indirme response'u olarak gönderir.
     *
     * $filename verilmezse dosyanın adı kullanılır.
     *
     * @param string $path gönderilecek dosyanın path'i.
     * @param ?string $filename istemciye gönderilecek dosya adı.
     *
     * @return self
     */
    public function download(string $path, ?string $filename = null): self;

    // --------------------------------------------------------------------------
    // HEADERS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen HTTP header değerini response'a ekler veya mevcut değeri günceller.
     *
     * @param string $key eklenecek header'ın adı.
     * @param string $value header'ın değeri.
     *
     * @return self
     */
    public function header(string $key, string $value): self;

    /**
     * Belirtilen HTTP header değerlerini response'a ekler veya mevcut değerleri
     * günceller.
     *
     * @param array<string, string> $headers eklenecek header adları ve değerleri.
     *
     * @return self
     */
    public function headers(array $headers): self;

    // --------------------------------------------------------------------------
    // COOKIES
    // --------------------------------------------------------------------------

    /**
     * Belirtilen cookie'yi response'a ekler.
     *
     * @param Cookie $cookie eklenecek cookie.
     *
     * @return self
     */
    public function cookie(Cookie $cookie): self;

    /**
     * Belirtilen cookie'nin response üzerinden kaldırılmasını sağlar.
     *
     * @param string $name kaldırılacak cookie'nin adı.
     *
     * @return self
     */
    public function forgetCookie(string $name): self;

    // --------------------------------------------------------------------------
    // JSON HELPERS
    // --------------------------------------------------------------------------

    /**
     * Başarılı bir JSON response oluşturur.
     *
     * @param string $message response mesajı.
     * @param array<mixed, mixed> $data response verileri.
     *
     * @return self
     */
    public function jsonSuccess(string $message, array $data = []): self;

    /**
     * Hatalı bir JSON response oluşturur.
     *
     * @param string $message response mesajı.
     * @param array<mixed, mixed> $errors response hataları.
     * @param int $status kullanılacak HTTP durum kodu.
     *
     * @return self
     */
    public function jsonError(string $message, array $errors = [], int $status = 400): self;

    // --------------------------------------------------------------------------
    // SUCCESS SHORTCUTS
    // --------------------------------------------------------------------------

    /**
     * Response durum kodunu 200 OK olarak ayarlar.
     *
     * İstek başarıyla işlendiğinde ve response ile birlikte bir içerik döndürüldüğünde
     * kullanılır.
     * 
     * @return self
     */
    public function ok(): self;

    /**
     * Response durum kodunu 201 Created olarak ayarlar.
     *
     * İstek sonucunda yeni bir kaynak oluşturulduğunda kullanılır.
     * 
     * @return self
     */
    public function created(): self;

    /**
     * Response durum kodunu 202 Accepted olarak ayarlar.
     *
     * İstek kabul edildiğinde ancak işlem henüz tamamlanmadığında kullanılır.
     * Özellikle işlemin asenkron olarak yürütüldüğü durumlar için uygundur.
     * 
     * @return self
     */
    public function accepted(): self;

    /**
     * Response durum kodunu 204 No Content olarak ayarlar.
     *
     * İstek başarıyla işlendiğinde ancak response gövdesinde bir içerik
     * döndürülmeyeceğinde kullanılır.
     *
     * @return self
     */
    public function noContent(): self;

    /**
     * Response durum kodunu 206 Partial Content olarak ayarlar.
     *
     * İstenen kaynağın tamamı yerine yalnızca belirli bir bölümü
     * gönderildiğinde kullanılır.
     * 
     * @return self
     */
    public function partialContent(): self;

    // --------------------------------------------------------------------------
    // REDIRECTION SHORTCUTS
    // --------------------------------------------------------------------------

    /**
     * Response durum kodunu 301 Moved Permanently olarak ayarlar ve belirtilen
     * URL'yi yönlendirme hedefi olarak belirler.
     *
     * Kaynak kalıcı olarak başka bir URL'ye taşındığında kullanılır.
     * 
     * @param string $url yönlendirme hedefi.
     *
     * @return self
     */
    public function movedPermanently(string $url): self;

    /**
     * Response durum kodunu 302 Found olarak ayarlar ve belirtilen URL'yi
     * yönlendirme hedefi olarak belirler.
     *
     * Kaynağın geçici olarak başka bir URL üzerinden sunulacağı durumlarda
     * kullanılır.
     *
     * @param string $url yönlendirme hedefi.
     *
     * @return self
     */
    public function found(string $url): self;

    /**
     * Response durum kodunu 303 See Other olarak ayarlar ve belirtilen URL'yi
     * yönlendirme hedefi olarak belirler.
     *
     * Yapılan isteğin sonucunun başka bir URL üzerinden GET isteğiyle
     * alınması gerektiğinde kullanılır.
     * 
     * @param string $url yönlendirme hedefi.
     *
     * @return self
     */
    public function seeOther(string $url): self;

    /**
     * Response durum kodunu 304 Not Modified olarak ayarlar.
     *
     * İstemcide bulunan kaynağın hâlâ güncel olduğu ve kaynağın yeniden
     * gönderilmesine gerek olmadığı durumlarda kullanılır.
     *
     * @return self
     */
    public function notModified(): self;

    /**
     * Response durum kodunu 307 Temporary Redirect olarak ayarlar ve belirtilen
     * URL'yi yönlendirme hedefi olarak belirler.
     *
     * Kaynak geçici olarak başka bir URL'ye taşındığında ve istemcinin mevcut
     * HTTP methodunu koruyarak yönlendirme yapması gerektiğinde kullanılır.
     *
     * @param string $url yönlendirme hedefi.
     *
     * @return self
     */
    public function temporaryRedirect(string $url): self;

    /**
     * Response durum kodunu 308 Permanent Redirect olarak ayarlar ve belirtilen
     * URL'yi yönlendirme hedefi olarak belirler.
     *
     * Kaynak kalıcı olarak başka bir URL'ye taşındığında ve istemcinin mevcut
     * HTTP methodunu koruyarak yönlendirme yapması gerektiğinde kullanılır.
     *
     * @param string $url yönlendirme hedefi.
     *
     * @return self
     */
    public function permanentRedirect(string $url): self;

    // --------------------------------------------------------------------------
    // CLIENT ERROR SHORTCUTS
    // --------------------------------------------------------------------------

    /**
     * Response durum kodunu 400 Bad Request olarak ayarlar.
     *
     * İstek geçersiz veya hatalı biçimde gönderildiğinde kullanılır.
     *
     * @return self
     */
    public function badRequest(): self;

    /**
     * Response durum kodunu 401 Unauthorized olarak ayarlar.
     *
     * Kimlik doğrulama gerektiğinde veya sağlanan kimlik bilgileri geçersiz
     * olduğunda kullanılır.
     *
     * @return self
     */
    public function unauthorized(): self;

    /**
     * Response durum kodunu 403 Forbidden olarak ayarlar.
     *
     * İstemcinin kaynağa erişim yetkisi olmadığında kullanılır.
     *
     * @return self
     */
    public function forbidden(): self;

    /**
     * Response durum kodunu 404 Not Found olarak ayarlar.
     *
     * İstenen kaynak mevcut olmadığında kullanılır.
     *
     * @return self
     */
    public function notFound(): self;

    /**
     * Response durum kodunu 405 Method Not Allowed olarak ayarlar.
     *
     * İstenen kaynak mevcut olmasına rağmen kullanılan HTTP methodu
     * desteklenmediğinde kullanılır.
     *
     * @return self
     */
    public function methodNotAllowed(): self;

    /**
     * Response durum kodunu 409 Conflict olarak ayarlar.
     *
     * İstek geçerli olmasına rağmen mevcut kaynak durumu ile çakıştığında
     * kullanılır.
     *
     * @return self
     */
    public function conflict(): self;

    /**
     * Response durum kodunu 415 Unsupported Media Type olarak ayarlar.
     *
     * İstek gövdesinin içerik türü desteklenmediğinde kullanılır.
     *
     * @return self
     */
    public function unsupportedMediaType(): self;

    /**
     * Response durum kodunu 422 Unprocessable Content olarak ayarlar.
     *
     * İstek yapısal olarak geçerli olmasına rağmen içerdiği veriler
     * işlenemediğinde kullanılır.
     *
     * @return self
     */
    public function unprocessableEntity(): self;

    /**
     * Response durum kodunu 429 Too Many Requests olarak ayarlar.
     *
     * İstemci izin verilen istek sayısını aştığında kullanılır.
     *
     * @return self
     */
    public function tooManyRequests(): self;

    // --------------------------------------------------------------------------
    // SERVER ERROR SHORTCUTS
    // --------------------------------------------------------------------------

    /**
     * Response durum kodunu 500 Internal Server Error olarak ayarlar.
     *
     * İstek işlenirken sunucuda beklenmeyen bir hata oluştuğunda kullanılır.
     * 
     * @return self
     */
    public function internalServerError(): self;

    /**
     * Response durum kodunu 501 Not Implemented olarak ayarlar.
     *
     * Sunucu, isteğin gerçekleştirilmesi için gereken işlevi desteklemediğinde
     * veya uygulamadığında kullanılır.
     * 
     * @return self
     */
    public function notImplemented(): self;

    /**
     * Response durum kodunu 503 Service Unavailable olarak ayarlar.
     *
     * Sunucu isteği geçici olarak işleyemediğinde kullanılır.
     * Örneğin bakım veya geçici servis yoğunluğu durumlarında kullanılabilir.
     *
     * @return self
     */
    public function serviceUnavailable(): self;

    // --------------------------------------------------------------------------
    // STATE
    // --------------------------------------------------------------------------

    /**
     * Response'un mevcut durumunu döndürür.
     *
     * @return ResponseState response durumu.
     */
    public function state(): ResponseState;

    // --------------------------------------------------------------------------
    // OUTPUT
    // --------------------------------------------------------------------------

    /**
     * Response'u istemciye gönderir.
     *
     * @return ResponseState gönderilen response'un durumu.
     */
    public function send(): ResponseState;
}
