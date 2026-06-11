<?php
// --------------------------------------------------------------------------===================================================
// File:    Response.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// --------------------------------------------------------------------------===================================================

namespace Seymenkonuk\Framework;


use Seymenkonuk\Framework\Exception\FileNotFoundException;


final class Response
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    private int $statusCode = 200;

    /** @var array<string, string> */
    private array $headers = [];

    /** @var array<array{
     *      name: string,
     *      value: string,
     *      expires: int,
     *      path: string,
     *      domain: string,
     *      secure: bool,
     *      httponly: bool
     * }> */
    private array $cookies = [];

    private string $body = "";

    private ?string $filePath = null;

    private bool $sent = false;

    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    public function __construct(
        private TemplateEngine $template,
    ) {}

    // --------------------------------------------------------------------------
    // VIEW RENDERING
    // --------------------------------------------------------------------------

    /** @param array<string, mixed> $data */
    public function view(string $viewName, array $data = []): self
    {
        $this->body = $this->template->render($viewName, $data);
        $this->headers["Content-Type"] = "text/html; charset=utf-8";
        return $this;
    }

    /** @param array<string, mixed> $data */
    public function component(string $componentName, array $data = []): self
    {
        $this->body = $this->template->renderComponent($componentName, $data);
        $this->headers["Content-Type"] = "text/html; charset=utf-8";
        return $this;
    }

    // --------------------------------------------------------------------------
    // BASIC RESPONSE TYPES
    // --------------------------------------------------------------------------

    /** @param array<mixed, mixed> $data */
    public function json(array $data = []): self
    {
        $this->body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $this->headers["Content-Type"] = "application/json; charset=utf-8";
        return $this;
    }

    public function html(string $content): self
    {
        $this->body = $content;
        $this->headers["Content-Type"] = "text/html; charset=utf-8";
        return $this;
    }

    public function text(string $content): self
    {
        $this->body = $content;
        $this->headers["Content-Type"] = "text/plain; charset=utf-8";
        return $this;
    }

    // --------------------------------------------------------------------------
    // REDIRECT
    // --------------------------------------------------------------------------

    public function redirect(string $url): self
    {
        $this->headers["Location"] = $url;

        if ($this->statusCode < 300 || $this->statusCode >= 400) {
            $this->statusCode = 302;
        }

        return $this;
    }

    // --------------------------------------------------------------------------
    // FILE RESPONSES
    // --------------------------------------------------------------------------

    public function file(string $path, ?string $contentType = null): self
    {
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        $this->filePath = $path;

        $this->header(
            "Content-Type",
            $contentType
                ?? mime_content_type($path)
                ?: "application/octet-stream"
        );

        return $this;
    }

    public function download(string $path, ?string $filename = null): self
    {
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        $filename ??= $path;
        $filename = basename($filename);

        $this->filePath = $path;

        $this->header(
            "Content-Type",
            "application/octet-stream"
        );

        $this->header(
            "Content-Disposition",
            'attachment; filename="' . $filename . '"'
        );

        return $this;
    }

    // --------------------------------------------------------------------------
    // HEADERS
    // --------------------------------------------------------------------------

    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /** @param array<string, string> $headers */
    public function headers(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
        return $this;
    }

    // --------------------------------------------------------------------------
    // STATUS CORE
    // --------------------------------------------------------------------------

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /** @param array<string, mixed> $data */
    public function abort(int $code, array $data = []): self
    {
        $this->statusCode = $code;
        $this->body = $this->template->renderError($code, $data);
        $this->headers["Content-Type"] = "text/html; charset=utf-8";
        return $this;
    }

    // --------------------------------------------------------------------------
    // JSON HELPERS
    // --------------------------------------------------------------------------

    /** @param array<string, mixed> $data */
    public function jsonSuccess(string $message, array $data = []): self
    {
        return $this->status(200)->json([
            "success" => true,
            "message" => $message,
            "data" => $data
        ]);
    }

    /** @param array<string, mixed> $errors */
    public function jsonError(string $message, array $errors = [], int $status = 400): self
    {
        return $this->status($status)->json([
            "success" => false,
            "message" => $message,
            "errors" => $errors
        ]);
    }

    // --------------------------------------------------------------------------
    // COOKIES
    // --------------------------------------------------------------------------

    public function cookie(
        string $name,
        string $value,
        int $maxAge = 0,
        string $path = "/",
        string $domain = "",
        bool $secure = false,
        bool $httpOnly = true
    ): self {

        $this->cookies[] = [
            "name" => $name,
            "value" => $value,
            "expires" => time() + $maxAge,
            "path" => $path,
            "domain" => $domain,
            "secure" => $secure,
            "httponly" => $httpOnly,
        ];

        return $this;
    }

    public function forgetCookie(string $name): self
    {
        return $this->cookie($name, "", -3600);
    }

    // --------------------------------------------------------------------------
    // GENERIC SHORTCUTS (200 OK family)
    // --------------------------------------------------------------------------

    public function ok(): self
    {
        return $this->status(200);
    }

    public function created(): self
    {
        return $this->status(201);
    }

    public function accepted(): self
    {
        return $this->status(202);
    }

    public function noContent(): self
    {
        return $this->status(204)->text("");
    }

    // --------------------------------------------------------------------------
    // REDIRECTION SHORTCUTS (3xx)
    // --------------------------------------------------------------------------

    public function multipleChoices(string $url): self
    {
        return $this->status(300)->redirect($url);
    }

    public function movedPermanently(string $url): self
    {
        return $this->status(301)->redirect($url);
    }

    public function found(string $url): self
    {
        return $this->status(302)->redirect($url);
    }

    public function seeOther(string $url): self
    {
        return $this->status(303)->redirect($url);
    }

    public function notModified(): self
    {
        return $this->status(304);
    }

    public function useProxy(string $url): self
    {
        return $this->status(305)->redirect($url);
    }

    public function temporaryRedirect(string $url): self
    {
        return $this->status(307)->redirect($url);
    }

    public function permanentRedirect(string $url): self
    {
        return $this->status(308)->redirect($url);
    }

    // --------------------------------------------------------------------------
    // CLIENT ERROR SHORTCUTS (4xx)
    // --------------------------------------------------------------------------

    public function badRequest(): self
    {
        return $this->status(400);
    }

    public function unauthorized(): self
    {
        return $this->status(401);
    }

    public function paymentRequired(): self
    {
        return $this->status(402);
    }

    public function forbidden(): self
    {
        return $this->status(403);
    }

    public function notFound(): self
    {
        return $this->status(404);
    }

    public function methodNotAllowed(): self
    {
        return $this->status(405);
    }

    public function conflict(): self
    {
        return $this->status(409);
    }

    public function unprocessableEntity(): self
    {
        return $this->status(422);
    }

    public function tooManyRequests(): self
    {
        return $this->status(429);
    }

    // --------------------------------------------------------------------------
    // SERVER ERROR SHORTCUTS (5xx)
    // --------------------------------------------------------------------------

    public function internalServerError(): self
    {
        return $this->status(500);
    }

    public function notImplemented(): self
    {
        return $this->status(501);
    }

    public function badGateway(): self
    {
        return $this->status(502);
    }

    public function serviceUnavailable(): self
    {
        return $this->status(503);
    }

    public function gatewayTimeout(): self
    {
        return $this->status(504);
    }

    // --------------------------------------------------------------------------
    // CORE OUTPUT
    // --------------------------------------------------------------------------

    public function send(): void
    {
        // Daha Önce Gönderildiyse Bir Daha Gönderme
        if ($this->sent) {
            return;
        }

        // Flag Set Et
        $this->sent = true;

        // Durum Kodunu Ayarla
        http_response_code($this->statusCode);

        // Headerları Ekle
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        // Cookieleri Ekle
        foreach ($this->cookies as $cookie) {
            setcookie(
                $cookie["name"],
                $cookie["value"],
                [
                    "expires" => $cookie["expires"],
                    "path" => $cookie["path"],
                    "domain" => $cookie["domain"],
                    "secure" => $cookie["secure"],
                    "httponly" => $cookie["httponly"],
                    "samesite" => "Lax",
                ]
            );
        }

        // Dosya Gönderilecekse
        if ($this->filePath !== null) {
            header("Content-Length: " . filesize($this->filePath));
            readfile($this->filePath);
            return;
        }

        // Body'i Yazdır
        echo $this->body;
    }
}
