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
    abstract protected function get(string $uri): ResponseState;

    /**
     * HTTP QUERY request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function query(string $uri, array $data): ResponseState;

    /**
     * HTTP POST request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function post(string $uri, array $data): ResponseState;

    /**
     * HTTP PUT request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function put(string $uri, array $data): ResponseState;

    /**
     * HTTP PATCH request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function patch(string $uri, array $data): ResponseState;

    /**
     * HTTP DELETE request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function delete(string $uri, array $data): ResponseState;

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
    abstract protected function getJson(string $uri): ResponseState;

    /**
     * JSON formatında HTTP QUERY request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function queryJson(string $uri, array $data): ResponseState;

    /**
     * JSON formatında HTTP POST request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function postJson(string $uri, array $data): ResponseState;

    /**
     * JSON formatında HTTP PUT request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function putJson(string $uri, array $data): ResponseState;

    /**
     * JSON formatında HTTP PATCH request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function patchJson(string $uri, array $data): ResponseState;

    /**
     * JSON formatında HTTP DELETE request gönderir.
     *
     * @param string $uri request URI'si.
     * @param array<string, mixed> $data gönderilecek request verileri.
     *
     * @return ResponseState oluşturulan response state.
     */
    abstract protected function deleteJson(string $uri, array $data): ResponseState;
}
