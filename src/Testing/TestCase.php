<?php
// ============================================================================
// File:    TestCase.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Testing;


use PHPUnit\Framework\TestCase as PHPUnitTestCase;

use Seymenkonuk\Framework\Application;
use Seymenkonuk\Framework\Http\Request\Request;
use Seymenkonuk\Framework\Http\Response\ResponseState;


abstract class TestCase extends PHPUnitTestCase
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    /**
     * Request ile gönderilecek header'ları tutar.
     *
     * @var array<string, string>
     */
    private array $headers = [];

    /**
     * Request ile gönderilecek cookie'leri tutar.
     *
     * @var array<string, mixed>
     */
    private array $cookies = [];

    /**
     * Request ile gönderilecek server parametrelerini tutar.
     *
     * @var array<string, string>
     */
    private array $servers = [];

    // --------------------------------------------------------------------------
    // SETUP
    // --------------------------------------------------------------------------

    /**
     * Test çalıştırılmadan önce test state'ini hazırlar.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->headers = [];
        $this->cookies = [];
        $this->servers = [];
    }

    // --------------------------------------------------------------------------
    // APPLICATION
    // --------------------------------------------------------------------------

    /**
     * Testlerde kullanılacak application'ı döndürür.
     *
     * @return Application testlerde kullanılacak application.
     */
    abstract protected function application(): Application;

    // --------------------------------------------------------------------------
    // REQUEST OPTIONS
    // --------------------------------------------------------------------------

    /**
     * Request'e cookie ekler.
     *
     * @param string $name cookie adı.
     * @param mixed $value cookie değeri.
     *
     * @return self
     */
    protected function withCookie(string $name, mixed $value): self
    {
        $this->cookies[$name] = $value;
        return $this;
    }

    /**
     * Request'e cookie'leri ekler.
     *
     * @param array<string, mixed> $cookies eklenecek cookie'ler.
     *
     * @return self
     */
    protected function withCookies(array $cookies): self
    {
        $this->cookies = array_merge($this->cookies, $cookies);
        return $this;
    }

    /**
     * Request'e header ekler.
     *
     * @param string $name header adı.
     * @param string $value header değeri.
     *
     * @return self
     */
    protected function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Request'e header'ları ekler.
     *
     * @param array<string, string> $headers eklenecek header'lar.
     *
     * @return self
     */
    protected function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Request'e server parametresi ekler.
     *
     * @param string $name server parametresinin adı.
     * @param string $value server parametresinin değeri.
     *
     * @return self
     */
    protected function withServer(string $name, string $value): self
    {
        $this->servers[$name] = $value;
        return $this;
    }

    /**
     * Request'e server parametrelerini ekler.
     *
     * @param array<string, string> $servers eklenecek server parametreleri.
     *
     * @return self
     */
    protected function withServers(array $servers): self
    {
        $this->servers = array_merge($this->servers, $servers);
        return $this;
    }

    // --------------------------------------------------------------------------
    // FORM REQUEST
    // --------------------------------------------------------------------------

    /**
     * HTTP GET request gönderir.
     *
     * @param string $uri request URI'si.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function get(string $uri): ResponseState
    {
        return $this->formRequest("GET", $uri, []);
    }

    /**
     * HTTP QUERY request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function query(string $uri, array $data): ResponseState
    {
        return $this->formRequest("QUERY", $uri, $data);
    }

    /**
     * HTTP POST request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function post(string $uri, array $data): ResponseState
    {
        return $this->formRequest("POST", $uri, $data);
    }

    /**
     * HTTP PUT request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function put(string $uri, array $data): ResponseState
    {
        return $this->formRequest("PUT", $uri, $data);
    }

    /**
     * HTTP PATCH request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function patch(string $uri, array $data): ResponseState
    {
        return $this->formRequest("PATCH", $uri, $data);
    }

    /**
     * HTTP DELETE request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function delete(string $uri, array $data): ResponseState
    {
        return $this->formRequest("DELETE", $uri, $data);
    }

    // --------------------------------------------------------------------------
    // JSON REQUEST
    // --------------------------------------------------------------------------

    /**
     * JSON formatında HTTP GET request gönderir.
     *
     * @param string $uri request URI'si.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function getJson(string $uri): ResponseState
    {
        return $this->jsonRequest("GET", $uri, []);
    }

    /**
     * JSON formatında HTTP QUERY request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function queryJson(string $uri, array $data): ResponseState
    {
        return $this->jsonRequest("QUERY", $uri, $data);
    }

    /**
     * JSON formatında HTTP POST request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function postJson(string $uri, array $data): ResponseState
    {
        return $this->jsonRequest("POST", $uri, $data);
    }

    /**
     * JSON formatında HTTP PUT request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function putJson(string $uri, array $data): ResponseState
    {
        return $this->jsonRequest("PUT", $uri, $data);
    }

    /**
     * JSON formatında HTTP PATCH request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function patchJson(string $uri, array $data): ResponseState
    {
        return $this->jsonRequest("PATCH", $uri, $data);
    }

    /**
     * JSON formatında HTTP DELETE request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    protected function deleteJson(string $uri, array $data): ResponseState
    {
        return $this->jsonRequest("DELETE", $uri, $data);
    }

    // --------------------------------------------------------------------------
    // INTERNAL
    // --------------------------------------------------------------------------

    /**
     * URI'den path bilgisini döndürür.
     *
     * @param string $uri request URI'si.
     *
     * @return string URI'nin path kısmı.
     */
    private function pathFromUri(string $uri): string
    {
        return parse_url("https://recepseymenkonuk.com" . $uri, PHP_URL_PATH) ?: "/";
    }

    /**
     * URI'den query parametrelerini döndürür.
     *
     * @param string $uri request URI'si.
     *
     * @return array<string, mixed> URI içerisindeki query parametreleri.
     */
    private function queryFromUri(string $uri): array
    {
        $query = parse_url("https://recepseymenkonuk.com" . $uri, PHP_URL_QUERY);

        if ($query === null || $query === "" || $query === false) {
            return [];
        }

        parse_str($query, $data);

        /** @phpstan-ignore return.type */
        return $data;
    }

    /**
     * Form verilerini URL encoded string'e dönüştürür.
     *
     * @param array<string, mixed> $data form verileri.
     *
     * @return string URL encoded form verisi.
     */
    private function encodeFormData(array $data): string
    {
        return http_build_query($data);
    }

    /**
     * Verileri JSON string'ine dönüştürür.
     *
     * @param array<string, mixed> $data JSON'a dönüştürülecek veriler.
     *
     * @return string JSON verisi.
     */
    private function encodeJsonData(array $data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );
    }

    /**
     * Form verisi içeren request gönderir.
     *
     * @param string $method HTTP request metodu.
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    private function formRequest(string $method, string $uri, array $data): ResponseState
    {
        return $this->application()
            ->run(new Request(
                version: "HTTP 1/1",
                method: $method,
                path: $this->pathFromUri($uri),
                headers: $this->headers,
                cookies: $this->cookies,
                body: $this->encodeFormData($data),
                post: $data,
                queries: $this->queryFromUri($uri),
                files: [],
                params: [],
                server: $this->servers,
            ), false);
    }

    /**
     * JSON verisi içeren request gönderir.
     *
     * @param string $method HTTP request metodu.
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    private function jsonRequest(string $method, string $uri, array $data): ResponseState
    {
        return $this->application()
            ->run(new Request(
                version: "HTTP 1/1",
                method: $method,
                path: $this->pathFromUri($uri),
                headers: $this->headers,
                cookies: $this->cookies,
                body: $this->encodeJsonData($data),
                post: [],
                queries: $this->queryFromUri($uri),
                files: [],
                params: [],
                server: $this->servers,
            ), false);
    }
}
