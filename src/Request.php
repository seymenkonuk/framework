<?php
// ============================================================================
// File:    Request.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


final class Request
{
    // --------------------------------------------------------------------------
    //  PROPERTIES
    // --------------------------------------------------------------------------

    /** @var array<string, mixed> */
    protected ?array $routeParams = null;
    
    // --------------------------------------------------------------------------
    //  CACHES
    // --------------------------------------------------------------------------

    /** @var array<string, string> */
    protected ?array $cachedHeaders = null;
    /** @var array<string, mixed> */
    protected ?array $cachedJson = null;
    protected ?string $cachedRawBody = null;

    // --------------------------------------------------------------------------
    //  ALL
    // --------------------------------------------------------------------------

    /** @return array{
     *      body: array<string, mixed>,
     *      query: array<string, mixed>,
     *      params: array<string, mixed>,
     *      files: array<string, UploadedFile>
     * } */
    public function all(): array
    {
        return [
            "body"   => $this->body(),
            "query"  => $this->allQuery(),
            "params" => $this->allRoute(),
            "files"  => $this->allFiles(),
        ];
    }

    // --------------------------------------------------------------------------
    //  BODY
    // --------------------------------------------------------------------------

    /** @return array<string, mixed> */
    public function body(): array
    {
        return $this->isJson()
            ? $this->allJson()
            : $this->allPost();
    }

    public function rawBody(): string
    {
        if ($this->cachedRawBody !== null) {
            return $this->cachedRawBody;
        }

        $body = file_get_contents("php://input");

        $this->cachedRawBody = ($body !== false)
            ? $body
            : "";

        return $this->cachedRawBody;
    }

    // --------------------------------------------------------------------------
    //  POST
    // --------------------------------------------------------------------------

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $this->allPost(), $default);
    }

    /** @return array<string, mixed> */
    public function allPost(): array
    {
        /** @var array<string, mixed> $_POST */
        return $_POST;
    }

    public function hasPost(string $key): bool
    {
        return $this->has($key, $this->allPost());
    }

    // --------------------------------------------------------------------------
    //  JSON
    // --------------------------------------------------------------------------

    public function json(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $this->allJson(), $default);
    }

    /** @return array<string, mixed> */
    public function allJson(): array
    {
        if ($this->cachedJson !== null) {
            return $this->cachedJson;
        }

        if (!$this->isJson()) {
            $this->cachedJson = [];
            return $this->cachedJson;
        }

        /** @var array<string, mixed>|null */
        $decoded = json_decode($this->rawBody(), true);

        $this->cachedJson = is_array($decoded)
            ? $decoded
            : [];

        return $this->cachedJson;
    }

    public function hasJson(string $key): bool
    {
        return $this->has($key, $this->allJson());
    }

    // --------------------------------------------------------------------------
    //  QUERY
    // --------------------------------------------------------------------------

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $this->allQuery(), $default);
    }

    /** @return array<string, mixed> */
    public function allQuery(): array
    {
        /** @var array<string, mixed> */
        return $_GET;
    }

    public function hasQuery(string $key): bool
    {
        return $this->has($key, $this->allQuery());
    }

    // --------------------------------------------------------------------------
    //  ROUTE PARAMETERS
    // --------------------------------------------------------------------------

    public function route(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $this->allRoute(), $default);
    }

    /** @return array<string, mixed> */
    public function allRoute(): array
    {
        return $this->routeParams ?? [];
    }

    public function hasRoute(string $key): bool
    {
        return $this->has($key, $this->allRoute());
    }

    /** @param array<string, mixed> $array */
    public function setRoutes(array $array): void
    {
        if ($this->routeParams === null) {
            $this->routeParams = $array;
        }
    }

    // --------------------------------------------------------------------------
    //  FILES
    // --------------------------------------------------------------------------

    public function file(string $key, ?UploadedFile $default = null): ?UploadedFile
    {
        // @phpstan-ignore-next-line
        return $this->get($key, $this->allFiles(), $default);
    }

    /** @return array<string, UploadedFile> */
    public function allFiles(): array
    {
        /** @var array<string, array{
         *     name: string,
         *     type: string,
         *     full_path?: string,
         *     tmp_name: string,
         *     error: int,
         *     size: int
         * }> $_FILES */
        return array_map(
            fn($file) => new UploadedFile($file),
            $_FILES,
        );
    }

    public function hasFile(string $key): bool
    {
        return $this->has($key, $this->allFiles());
    }

    // --------------------------------------------------------------------------
    //  HEADERS
    // --------------------------------------------------------------------------

    public function header(string $key, string $default = ""): string
    {
        // @phpstan-ignore-next-line
        return $this->get(strtolower($key), $this->allHeader(), $default);
    }

    /** @return array<string, string> */
    public function allHeader(): array
    {
        // Cache'de Varsa Onu Ver
        if ($this->cachedHeaders !== null) {
            return $this->cachedHeaders;
        }

        // Hazır Fonk Varsa Oradan Al
        if (function_exists("getallheaders")) {

            $headers = getallheaders();

            // @phpstan-ignore-next-line
            if ($headers !== false) {

                // @phpstan-ignore-next-line
                $this->cachedHeaders = array_change_key_case(
                    $headers,
                    CASE_LOWER
                );

                // @phpstan-ignore-next-line
                return $this->cachedHeaders;
            }
        }

        // Hazır Fonk Yok Kendin Parse Et
        $this->cachedHeaders = $this->parseHeaders();

        return $this->cachedHeaders;
    }

    public function hasHeader(string $key): bool
    {
        return $this->has(strtolower($key), $this->allHeader());
    }

    /** @return array<string, string> */
    private function parseHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, "HTTP_")) {
                $header = substr($key, 5);
                $header = str_replace("_", "-", $header);
                $header = strtolower($header);

                $headers[$header] = $value;
            }
        }

        // Special Cases
        if (isset($_SERVER["CONTENT_TYPE"])) {
            $headers["content-type"] = $_SERVER["CONTENT_TYPE"];
        }

        if (isset($_SERVER["CONTENT_LENGTH"])) {
            $headers["content-length"] = $_SERVER["CONTENT_LENGTH"];
        }

        // @phpstan-ignore-next-line
        return $headers;
    }

    // --------------------------------------------------------------------------
    //  COOKIES
    // --------------------------------------------------------------------------

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $this->allCookie(), $default);
    }

    /** @return array<string, mixed> */
    public function allCookie(): array
    {
        /** @var array<string, mixed> $_COOKIE */
        return $_COOKIE;
    }

    public function hasCookie(string $key): bool
    {
        return $this->has($key, $this->allCookie());
    }

    // --------------------------------------------------------------------------
    //  HTTP INFO
    // --------------------------------------------------------------------------

    public function method(): string
    {
        // @phpstan-ignore-next-line
        return strtoupper($_SERVER["REQUEST_METHOD"] ?? "GET");
    }

    public function uri(): string
    {
        /** @var string $uri */
        $uri = $_SERVER["REQUEST_URI"] ?? "/";
        return parse_url("https://recepseymenkonuk.com" . $uri, PHP_URL_PATH) ?: "/";
    }

    public function url(): string
    {
        $scheme = (
            !empty($_SERVER["HTTPS"])
            && $_SERVER["HTTPS"] !== "off"
        )
            ? "https"
            : "http";

        /** @var string $host */
        $host = $_SERVER["HTTP_HOST"] ?? "localhost";

        /** @var string $uri */
        $uri = $this->uri();

        return $scheme . "://" . $host . $uri;
    }

    public function ip(): string
    {
        /** @var string $result */
        $result = $_SERVER["REMOTE_ADDR"] ?? "0.0.0.0";
        return $result;
    }

    public function userAgent(): ?string
    {
        /** @var ?string $result */
        $result = $_SERVER["HTTP_USER_AGENT"] ?? null;
        return $result;
    }

    // --------------------------------------------------------------------------
    //  HELPERS
    // --------------------------------------------------------------------------

    public function isAjax(): bool
    {
        // @phpstan-ignore-next-line
        return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";
    }

    public function isJson(): bool
    {
        /** @var string $contentType  */
        $contentType = $_SERVER["CONTENT_TYPE"] ?? "";
        return str_contains($contentType, "application/json");
    }

    public function expectsJson(): bool
    {
        /** @var string $accept */
        $accept = $_SERVER["HTTP_ACCEPT"] ?? "";

        return str_contains($accept, "application/json")
            || $this->isAjax();
    }

    /** @param array<string, mixed> $data */
    private function get(string $key, array $data, mixed $default = null): mixed
    {
        return $this->has($key, $data)
            ? $data[$key]
            : $default;
    }

    /** @param array<string, mixed> $data */
    private function has(string $key, array $data): bool
    {
        return array_key_exists($key, $data);
    }
}
