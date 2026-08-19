<?php
// ============================================================================
// File:    Request.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Request;


use Seymenkonuk\Framework\Http\UploadedFile\IUploadedFile;
use Seymenkonuk\Framework\Http\UploadedFile\PhpUploadedFile;


final class Request implements IRequest
{
    // --------------------------------------------------------------------------
    //  CACHES
    // --------------------------------------------------------------------------

    /**
     * Ham request body'den parse edilen JSON verilerini saklar.
     *
     * İlk JSON erişiminde parse edilen veri burada saklanır ve sonraki
     * erişimlerde request body yeniden parse edilmez.
     *
     * null değeri JSON verisinin henüz parse edilmediğini belirtir.
     *
     * @var ?array<string, mixed>
     */
    protected ?array $cachedJson = null;

    // --------------------------------------------------------------------------
    //  CONSTRUCTOR
    // --------------------------------------------------------------------------

    /**
     * Yeni bir request oluşturur.
     *
     * @param string $version HTTP protokol sürümü.
     * @param string $method HTTP request metodu.
     * @param string $path request'in path bilgisi.
     * @param array<string, string> $headers HTTP header adları ve değerleri.
     * @param array<string, mixed> $cookies request ile gönderilen cookie'ler.
     * @param string $body request body içeriği.
     * @param array<string, mixed> $post parse edilen POST verileri.
     * @param array<string, mixed> $queries parse edilen query parametreleri.
     * @param array<string, IUploadedFile> $files request ile gönderilen dosyalar.
     * @param array<string, mixed> $params route tarafından eşleştirilen parametreler.
     * @param array<string, mixed> $server sunucu ve request ortamına ait bilgiler.
     *
     * @return void
     */
    public function __construct(
        protected string $version,
        protected string $method,
        protected string $path,
        protected array $headers,
        protected array $cookies,
        protected string $body,
        protected array $post,
        protected array $queries,
        protected array $files,
        protected array $params,
        protected array $server,
    ) {}

    // --------------------------------------------------------------------------
    // FACTORIES
    // --------------------------------------------------------------------------

    /**
     * Mevcut PHP request verilerinden yeni bir request oluşturur.
     *
     * @return static oluşturulan request.
     */
    public static function capture(): static
    {
        $version = $_SERVER["SERVER_PROTOCOL"];
        $version = is_string($version) ? $version : "HTTP/1.1";

        $method = $_SERVER["REQUEST_METHOD"];
        $method = is_string($method) ? strtoupper($method) : "GET";

        $uri = $_SERVER["REQUEST_URI"];
        $uri = is_string($uri) ? $uri : "/";
        $path = parse_url("https://recepseymenkonuk.com" . $uri, PHP_URL_PATH) ?: "/";

        /** @var array<string, string> */
        $headers = self::parseHeader();

        /** @var array<string, mixed> */
        $cookies = $_COOKIE;

        /** @var array<string, mixed> */
        $post = $_POST;

        /** @var array<string, mixed> */
        $queries = $_GET;

        /** @var array<string, PhpUploadedFile> */
        $files = array_map(function ($file) {
            return new PhpUploadedFile($file); // @phpstan-ignore argument.type
        }, $_FILES);

        /** @var array<string, mixed> */
        $server = $_SERVER;

        return new static(
            version: $version,
            method: $method,
            path: $path,
            headers: $headers,
            cookies: $cookies,
            body: file_get_contents("php://input") ?: "",
            post: $post,
            queries: $queries,
            files: $files,
            params: [],
            server: $server,
        );
    }

    /**
     * Sunucu değişkenlerinden HTTP header'larını ayrıştırır.
     *
     * `HTTP_` ile başlayan sunucu değişkenleri header olarak kabul edilir.
     * Header adlarındaki alt çizgiler tireye çevrilir ve her kelimenin ilk
     * harfi büyük olacak şekilde normalize edilir.
     *
     * @return array<string, string> ayrıştırılmış HTTP header'ları.
     */
    private static function parseHeader(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (!str_starts_with($key, "HTTP_")) {
                continue;
            }

            $name = substr($key, 5);
            $name = str_replace("_", "-", strtolower($name));
            $name = ucwords($name, "-");

            $headers[$name] = (string) $value; // @phpstan-ignore cast.string
        }

        if (isset($_SERVER["CONTENT_TYPE"])) {
            $headers["Content-Type"] = (string) $_SERVER["CONTENT_TYPE"]; // @phpstan-ignore cast.string
        }

        if (isset($_SERVER["CONTENT_LENGTH"])) {
            $headers["Content-Length"] = (string) $_SERVER["CONTENT_LENGTH"]; // @phpstan-ignore cast.string
        }

        return $headers;
    }

    // --------------------------------------------------------------------------
    //  HTTP INFO
    // --------------------------------------------------------------------------

    public function version(): string
    {
        return $this->version;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function url(): string
    {
        /** @var string */
        $https = $this->server("HTTPS", "off");
        $scheme = $https !== "off"
            ? "https"
            : "http";

        $host = $this->header("Host", "localhost");

        $path = $this->path();

        return $scheme . "://" . $host . $path;
    }

    public function ip(): string
    {
        return $this->server("REMOTE_ADDR", "0.0.0.0");
    }

    public function userAgent(): ?string
    {
        $userAgent = $this->header("User-Agent", "");
        return $userAgent !== "" ? $userAgent : null;
    }

    // --------------------------------------------------------------------------
    //  HEADERS
    // --------------------------------------------------------------------------

    public function header(string $key, string $default = ""): string
    {
        if (!$this->hasHeader($key)) {
            return $default;
        }
        return $this->headers[$key];
    }

    public function allHeader(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $key): bool
    {
        return array_key_exists($key, $this->headers);
    }

    // --------------------------------------------------------------------------
    //  COOKIES
    // --------------------------------------------------------------------------

    public function cookie(string $key, mixed $default = null): mixed
    {
        if (!$this->hasCookie($key)) {
            return $default;
        }
        return $this->cookies[$key];
    }

    public function allCookie(): array
    {
        return $this->cookies;
    }

    public function hasCookie(string $key): bool
    {
        return array_key_exists($key, $this->cookies);
    }

    // --------------------------------------------------------------------------
    //  ALL
    // --------------------------------------------------------------------------

    public function all(): array
    {
        return [
            "version" => $this->version(),
            "method" => $this->method(),
            "path" => $this->path(),
            "headers" => $this->allHeader(),
            "cookies" => $this->allCookie(),
            "body" => $this->body(),
            "query" => $this->allQuery(),
            "params" => $this->allParam(),
            "files" => $this->allFiles(),
            "server" => $this->allServer(),
        ];
    }

    // --------------------------------------------------------------------------
    //  BODY
    // --------------------------------------------------------------------------

    public function rawBody(): string
    {
        return $this->body;
    }

    public function body(): array
    {
        return $this->isJson()
            ? $this->allJson()
            : $this->allPost();
    }

    // --------------------------------------------------------------------------
    //  POST
    // --------------------------------------------------------------------------

    public function post(string $key, mixed $default = null): mixed
    {
        if (!$this->hasPost($key)) {
            return $default;
        }
        return $this->allPost()[$key];
    }

    public function allPost(): array
    {
        return $this->post;
    }

    public function hasPost(string $key): bool
    {
        return array_key_exists($key, $this->allPost());
    }

    // --------------------------------------------------------------------------
    //  JSON
    // --------------------------------------------------------------------------

    public function json(string $key, mixed $default = null): mixed
    {
        if (!$this->hasJson($key)) {
            return $default;
        }
        return $this->allJson()[$key];
    }

    public function allJson(): array
    {
        if ($this->cachedJson !== null) {
            return $this->cachedJson;
        }

        if (!$this->isJson()) {
            $this->cachedJson = [];
            return $this->cachedJson;
        }

        $decoded = json_decode($this->rawBody(), true);

        if (!is_array($decoded)) {
            $this->cachedJson = [];
            return $this->cachedJson;
        }

        /** @var array<string, mixed> $decoded */
        $this->cachedJson = $decoded;
        return $this->cachedJson;
    }

    public function hasJson(string $key): bool
    {
        return array_key_exists($key, $this->allJson());
    }

    // --------------------------------------------------------------------------
    //  QUERY
    // --------------------------------------------------------------------------

    public function query(string $key, mixed $default = null): mixed
    {
        if (!$this->hasQuery($key)) {
            return $default;
        }
        return $this->queries[$key];
    }

    public function allQuery(): array
    {
        return $this->queries;
    }

    public function hasQuery(string $key): bool
    {
        return array_key_exists($key, $this->queries);
    }

    // --------------------------------------------------------------------------
    //  ROUTE PARAMETERS
    // --------------------------------------------------------------------------

    public function param(string $key, mixed $default = null): mixed
    {
        if (!$this->hasParam($key)) {
            return $default;
        }
        return $this->params[$key];
    }

    public function allParam(): array
    {
        return $this->params;
    }

    public function hasParam(string $key): bool
    {
        return array_key_exists($key, $this->params);
    }

    // --------------------------------------------------------------------------
    //  FILES
    // --------------------------------------------------------------------------

    public function file(string $key, ?IUploadedFile $default = null): ?IUploadedFile
    {
        if (!$this->hasFile($key)) {
            return $default;
        }
        return $this->files[$key];
    }

    public function allFiles(): array
    {
        return $this->files;
    }

    public function hasFile(string $key): bool
    {
        return array_key_exists($key, $this->files);
    }

    // --------------------------------------------------------------------------
    //  SERVER
    // --------------------------------------------------------------------------

    public function server(string $key, mixed $default = null): mixed
    {
        if (!$this->hasServer($key)) {
            return $default;
        }
        return $this->server[$key];
    }

    public function allServer(): array
    {
        return $this->server;
    }

    public function hasServer(string $key): bool
    {
        return array_key_exists($key, $this->server);
    }

    // --------------------------------------------------------------------------
    //  HELPERS
    // --------------------------------------------------------------------------

    public function isAjax(): bool
    {
        return strtolower($this->header("X-Requested-With", "")) === "xmlhttprequest";
    }

    public function isJson(): bool
    {
        $contentType = strtolower($this->header("Content-Type", ""));
        return str_contains($contentType, "application/json");
    }

    public function isFormUrlEncoded(): bool
    {
        $contentType = strtolower($this->header("Content-Type", ""));
        return str_contains($contentType, "application/x-www-form-urlencoded");
    }

    public function expectsJson(): bool
    {
        $accept = strtolower($this->header("Accept", ""));

        return str_contains($accept, "application/json")
            || $this->isAjax();
    }

    // --------------------------------------------------------------------------
    // DERIVATION
    // --------------------------------------------------------------------------

    public function with(array $data): IRequest
    {
        $rawBody = $this->rawBody();
        $post = $this->allPost();
        if (isset($data["body"])) {
            if ($this->isJson()) {
                $rawBody = json_encode($data["body"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            } else {
                $post = $data["body"];
                $rawBody = http_build_query($post);
            }
        }

        return new Request(
            version: $data["version"] ?? $this->version(),
            method: $data["method"] ?? $this->method(),
            path: $data["path"] ?? $this->path(),
            headers: $data["headers"] ?? $this->allHeader(),
            cookies: $data["cookies"] ?? $this->allCookie(),
            body: $rawBody,
            post: $post,
            queries: $data["query"] ?? $this->allQuery(),
            files: $data["files"] ?? $this->allFiles(),
            params: $data["params"] ?? $this->allParam(),
            server: $data["server"] ?? $this->allServer(),
        );
    }
}
