<?php
// --------------------------------------------------------------------------===================================================
// File:    Response.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// --------------------------------------------------------------------------===================================================

namespace Seymenkonuk\Framework\Http\Response;


use Seymenkonuk\Framework\Exception\FileNotFoundException;
use Seymenkonuk\Framework\Http\Cookie\Cookie;
use Seymenkonuk\Framework\TemplateEngine\ITemplateEngine;


final class Response implements IResponse
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    /**
     * Response için kullanılan HTTP durum kodunu saklar.
     * 
     * @var int
     */
    protected int $statusCode = 200;

    /**
     * Response'a ait HTTP header değerlerini saklar.
     *
     * @var array<string, string>
     */
    protected array $headers = [];

    /**
     * Response'a ait cookie'leri saklar.
     *
     * @var array<string, Cookie>
     */
    protected array $cookies = [];

    /**
     * Response gövdesinin içeriğini saklar.
     * 
     * @var string
     */
    protected string $body = "";

    /**
     * Response olarak gönderilecek dosyanın path bilgisini saklar.
     * 
     * @var ?string
     */
    protected ?string $filePath = null;

    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    /**
     * Yeni bir response oluşturur.
     *
     * @param ITemplateEngine $template response içeriğini oluşturmak için kullanılan template engine.
     *
     * @return void
     */
    public function __construct(
        protected ITemplateEngine $template,
    ) {}

    // --------------------------------------------------------------------------
    // VIEW RENDERING
    // --------------------------------------------------------------------------

    public function view(string $viewName, array $data = []): self
    {
        return $this->content(
            content: $this->template->render($viewName, $data),
            contentType: "text/html; charset=utf-8",
        );
    }

    public function component(string $componentName, array $data = []): self
    {
        return $this->content(
            content: $this->template->renderComponent($componentName, $data),
            contentType: "text/html; charset=utf-8",
        );
    }

    // --------------------------------------------------------------------------
    // CONTENT
    // --------------------------------------------------------------------------

    public function content(string $content, string $contentType): self
    {
        $this->body = $content;
        return $this->header("Content-Type", $contentType);
    }

    public function json(array $data = []): self
    {
        return $this->content(
            content: json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            contentType: "application/json; charset=utf-8",
        );
    }

    public function html(string $content): self
    {
        return $this->content(
            content: $content,
            contentType: "text/html; charset=utf-8",
        );
    }

    public function text(string $content): self
    {
        return $this->content(
            content: $content,
            contentType: "text/plain; charset=utf-8",
        );
    }

    // --------------------------------------------------------------------------
    // STATUS
    // --------------------------------------------------------------------------

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function abort(int $code, array $data = []): self
    {
        return $this->content(
            content: $this->template->renderError($code, $data),
            contentType: "text/html; charset=utf-8",
        )->status($code);
    }

    // --------------------------------------------------------------------------
    // REDIRECT
    // --------------------------------------------------------------------------

    public function redirect(string $url): self
    {
        if ($this->statusCode < 300 || $this->statusCode >= 400) {
            $this->status(302);
        }
        return $this->header("Location", $url);
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
        $this->body = "";

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
        $this->body = "";

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

    public function headers(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }
        return $this;
    }

    // --------------------------------------------------------------------------
    // COOKIES
    // --------------------------------------------------------------------------

    public function cookie(Cookie $cookie): self
    {
        $this->cookies[$cookie->name()] = $cookie;
        return $this;
    }

    public function forgetCookie(string $name, string $path = "/", string $domain = ""): self
    {
        return $this->cookie(Cookie::forget($name, $path, $domain));
    }

    // --------------------------------------------------------------------------
    // JSON HELPERS
    // --------------------------------------------------------------------------

    public function jsonSuccess(string $message, array $data = []): self
    {
        return $this->status(200)->json([
            "success" => true,
            "message" => $message,
            "data" => $data
        ]);
    }

    public function jsonError(string $message, array $errors = [], int $status = 400): self
    {
        return $this->status($status)->json([
            "success" => false,
            "message" => $message,
            "errors" => $errors
        ]);
    }

    // --------------------------------------------------------------------------
    // SUCCESS SHORTCUTS
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

    public function partialContent(): self
    {
        return $this->status(206);
    }

    // --------------------------------------------------------------------------
    // REDIRECTION SHORTCUTS
    // --------------------------------------------------------------------------

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

    public function temporaryRedirect(string $url): self
    {
        return $this->status(307)->redirect($url);
    }

    public function permanentRedirect(string $url): self
    {
        return $this->status(308)->redirect($url);
    }

    // --------------------------------------------------------------------------
    // CLIENT ERROR SHORTCUTS
    // --------------------------------------------------------------------------

    public function badRequest(): self
    {
        return $this->status(400);
    }

    public function unauthorized(): self
    {
        return $this->status(401);
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

    public function unsupportedMediaType(): self
    {
        return $this->status(415);
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
    // SERVER ERROR SHORTCUTS
    // --------------------------------------------------------------------------

    public function internalServerError(): self
    {
        return $this->status(500);
    }

    public function notImplemented(): self
    {
        return $this->status(501);
    }

    public function serviceUnavailable(): self
    {
        return $this->status(503);
    }

    // --------------------------------------------------------------------------
    // STATE
    // --------------------------------------------------------------------------

    public function state(): ResponseState
    {
        return new ResponseState(
            $this->statusCode,
            $this->headers,
            $this->cookies,
            $this->body,
            $this->filePath,
        );
    }

    // --------------------------------------------------------------------------
    // OUTPUT
    // --------------------------------------------------------------------------

    public function send(): ResponseState
    {
        // Durum Kodunu Ayarla
        http_response_code($this->statusCode);

        // Headerları Ekle
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        // Cookieleri Ekle
        foreach ($this->cookies as $cookie) {
            setcookie(
                $cookie->name(),
                is_scalar($cookie->value())
                    ? (string) $cookie->value()
                    : serialize($cookie->value()),
                $cookie->expires(),
                $cookie->path(),
                $cookie->domain(),
                $cookie->secure(),
                $cookie->httpOnly(),
            );
        }

        // Dosya Gönderilecekse
        if ($this->filePath !== null) {
            header("Content-Length: " . filesize($this->filePath));
            readfile($this->filePath);
        } else {
            echo $this->body;
        }

        return $this->state();
    }
}
