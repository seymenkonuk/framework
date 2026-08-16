<?php
// ============================================================================
// File:    Route.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Routing;


use Seymenkonuk\Framework\Http\Controller;
use Seymenkonuk\Framework\Http\Middleware;
use Seymenkonuk\Framework\Http\RequestSchema\IRequestSchema;
use Seymenkonuk\Framework\Http\Response\IResponse;


final class Route
{
    // ------------------------------------------------------------------
    // CONSTRUCTOR
    // ------------------------------------------------------------------

    /**
     * Belirtilen bilgilerle bir route tanımı oluşturur.
     *
     * @param array<string> $methods route tarafından kabul edilen HTTP metotları.
     * @param string $uri route'un URI deseni.
     * @param array{class-string<Controller>, string}|callable(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     * @param array<class-string<Middleware>> $middleware route'a uygulanacak middleware sınıfları.
     * @param array<string, string> $where URI parametreleri için kullanılacak regex kuralları.
     * @param ?class-string<IRequestSchema> $schema isteği doğrulamak için kullanılacak schema sınıfı.
     * @param ?string $name route'un adı.
     *
     * @return void
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

    /**
     * Route'a middleware ekler.
     *
     * Tek bir middleware veya birden fazla middleware verilebilir.
     *
     * @param array<class-string<Middleware>>|class-string<Middleware> $middleware eklenecek middleware sınıfı veya sınıfları.
     *
     * @return self güncellenmiş route.
     */
    public function middleware(array|string $middleware): self
    {
        $this->middleware = array_merge($this->middleware, (array) $middleware);
        return $this;
    }

    // ------------------------------------------------------------------
    // WHERE (PARAM REGEX)
    // ------------------------------------------------------------------

    /**
     * URI parametresi için regex kuralı tanımlar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     * @param string $pattern kullanılacak regex deseni.
     *
     * @return self güncellenmiş route.
     */
    public function where(string $key, string $pattern): self
    {
        $this->where[$key] = $pattern;
        return $this;
    }

    /**
     * URI parametresinin yalnızca sayılardan oluşmasını zorunlu kılar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     *
     * @return self güncellenmiş route.
     */
    public function whereNumber(string $key): self
    {
        return $this->where($key, "[0-9]+");
    }

    /**
     * URI parametresinin yalnızca hexadecimal karakterlerden oluşmasını zorunlu kılar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     *
     * @return self güncellenmiş route.
     */
    public function whereHex(string $key): self
    {
        return $this->where($key, "[0-9a-f]+");
    }

    /**
     * URI parametresinin yalnızca alfabetik karakterlerden oluşmasını zorunlu kılar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     *
     * @return self güncellenmiş route.
     */
    public function whereAlpha(string $key): self
    {
        return $this->where($key, "[a-zA-Z]+");
    }

    /**
     * URI parametresinin yalnızca harf ve rakamlardan oluşmasını zorunlu kılar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     *
     * @return self güncellenmiş route.
     */
    public function whereAlphaNumeric(string $key): self
    {
        return $this->where($key, "[a-zA-Z0-9]+");
    }

    /**
     * URI parametresinin slug formatında olmasını zorunlu kılar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     *
     * @return self güncellenmiş route.
     */
    public function whereSlug(string $key): self
    {
        return $this->where($key, "[a-zA-Z0-9\-]+");
    }

    /**
     * URI parametresinin geçerli bir UUID olmasını zorunlu kılar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     *
     * @return self güncellenmiş route.
     */
    public function whereUuid(string $key): self
    {
        return $this->where(
            $key,
            "[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}"
        );
    }

    /**
     * URI parametresinin geçerli bir ULID olmasını zorunlu kılar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     *
     * @return self güncellenmiş route.
     */
    public function whereUlid(string $key): self
    {
        return $this->where(
            $key,
            "[0-9A-HJKMNP-TV-Z]{26}"
        );
    }

    /**
     * URI parametresinin hash formatında olmasını zorunlu kılar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     *
     * @return self güncellenmiş route.
     */
    public function whereHash(string $key): self
    {
        return $this->where($key, "[a-fA-F0-9]+");
    }

    /**
     * URI parametresinin herhangi bir değeri kabul etmesini sağlar.
     *
     * @param string $key kuralın uygulanacağı URI parametresinin adı.
     *
     * @return self güncellenmiş route.
     */
    public function whereAny(string $key): self
    {
        return $this->where($key, ".+");
    }

    /**
     * Birden fazla URI parametresi için regex kurallarını aynı anda tanımlar.
     *
     * @param array<string, string> $rules parametre adları ve regex kuralları.
     *
     * @return self güncellenmiş route.
     */
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

    /**
     * Route için kullanılacak request schema'sını tanımlar.
     *
     * Schema, route'a gelen isteği doğrulamak için kullanılır.
     *
     * @param class-string<IRequestSchema> $schema kullanılacak request schema sınıfı.
     *
     * @return self güncellenmiş route.
     */
    public function schema(string $schema): self
    {
        $this->schema = $schema;
        return $this;
    }

    // ------------------------------------------------------------------
    // NAME
    // ------------------------------------------------------------------

    /**
     * Route'a isim verir.
     *
     * @param string $name route'un adı.
     *
     * @return self güncellenmiş route.
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
