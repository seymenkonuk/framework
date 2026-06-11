<?php
// ============================================================================
// File:    Route.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


final class Route
{
    // ------------------------------------------------------------------
    // CONSTRUCTOR
    // ------------------------------------------------------------------

    /**
     * @param array<string> $methods
     * @param array{0: string, 1: string} $handler
     * @param array<string> $middleware
     * @param array<string, string> $where
     */
    public function __construct(
        public array $methods,
        public string $uri,
        public array $handler,
        public array $middleware = [],
        public array $where = [],
        public ?string $schema = null,
        public ?string $name = null,
    ) {}

    // ------------------------------------------------------------------
    // MIDDLEWARE
    // ------------------------------------------------------------------

    /** @param array<string>|string $middleware */
    public function middleware(array|string $middleware): self
    {
        $this->middleware = array_merge($this->middleware, (array) $middleware);
        return $this;
    }

    // ------------------------------------------------------------------
    // WHERE (PARAM REGEX)
    // ------------------------------------------------------------------

    public function where(string $key, string $pattern): self
    {
        $this->where[$key] = $pattern;
        return $this;
    }

    public function whereNumber(string $key): self
    {
        return $this->where($key, "[0-9]+");
    }

    public function whereHex(string $key): self
    {
        return $this->where($key, "[0-9a-f]+");
    }

    public function whereAlpha(string $key): self
    {
        return $this->where($key, "[a-zA-Z]+");
    }

    public function whereAlphaNumeric(string $key): self
    {
        return $this->where($key, "[a-zA-Z0-9]+");
    }

    public function whereSlug(string $key): self
    {
        return $this->where($key, "[a-zA-Z0-9\-]+");
    }

    public function whereUuid(string $key): self
    {
        return $this->where(
            $key,
            "[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}"
        );
    }

    public function whereUlid(string $key): self
    {
        return $this->where(
            $key,
            "[0-9A-HJKMNP-TV-Z]{26}"
        );
    }

    public function whereHash(string $key): self
    {
        return $this->where($key, "[a-fA-F0-9]+");
    }

    public function whereAny(string $key): self
    {
        return $this->where($key, ".+");
    }

    /** @param array<string, string> $rules */
    public function whereMany(array $rules): self
    {
        foreach ($rules as $key => $pattern) {
            $this->where($key, $pattern);
        }
        return $this;
    }

    // ------------------------------------------------------------------
    // SCHEMA
    // ------------------------------------------------------------------

    public function schema(?string $schema): self
    {
        $this->schema = $schema;
        return $this;
    }

    // ------------------------------------------------------------------
    // NAME
    // ------------------------------------------------------------------

    public function name(?string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
