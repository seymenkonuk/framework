<?php
// ============================================================================
// File:    RouteState.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Routing;


use Closure;

use Seymenkonuk\Framework\Http\Controller;
use Seymenkonuk\Framework\Http\Middleware;
use Seymenkonuk\Framework\Http\RequestSchema\IRequestSchema;
use Seymenkonuk\Framework\Http\Response\IResponse;


final class RouteState
{
    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    /**
     * Route state'ini oluşturur.
     *
     * @param array<string> $methods route tarafından kabul edilen HTTP metotları.
     * @param string $uri route'un URI deseni.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     * @param array<class-string<Middleware>> $middleware route'a uygulanacak middleware sınıfları.
     * @param array<string, string> $where URI parametreleri için kullanılacak regex kuralları.
     * @param ?class-string<IRequestSchema> $schema isteği doğrulamak için kullanılacak schema sınıfı.
     * @param ?string $name route'un adı.
     *
     * @return void
     */
    public function __construct(
        protected readonly array $methods,
        protected readonly string $uri,
        protected readonly array|Closure $handler,
        protected readonly array $middleware = [],
        protected readonly array $where = [],
        protected readonly ?string $schema = null,
        protected readonly ?string $name = null,
    ) {}

    // --------------------------------------------------------------------------
    // METHODS
    // --------------------------------------------------------------------------

    /**
     * Route tarafından kabul edilen HTTP metotlarını döndürür.
     *
     * @return array<string> route tarafından kabul edilen HTTP metotları.
     */
    public function methods(): array
    {
        return $this->methods;
    }

    /**
     * Route'un URI desenini döndürür.
     *
     * @return string route'un URI deseni.
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * Route handler'ını döndürür.
     *
     * @return array{class-string<Controller>, string}|Closure(mixed...): IResponse route çalıştırıldığında çağrılacak handler.
     */
    public function handler(): array|Closure
    {
        return $this->handler;
    }

    /**
     * Belirtilen middleware sınıfını döndürür.
     *
     * Middleware mevcut değilse null döndürülür.
     *
     * @param int $id okunacak middleware'in index'i.
     *
     * @return ?class-string<Middleware> middleware sınıfı veya null.
     */
    public function middleware(int $id): ?string
    {
        return $this->middleware[$id] ?? null;
    }

    /**
     * Route'a uygulanacak tüm middleware sınıflarını döndürür.
     *
     * @return array<class-string<Middleware>> route'a uygulanacak middleware sınıfları.
     */
    public function allMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Belirtilen URI parametresi için regex kuralını döndürür.
     *
     * Kural mevcut değilse null döndürülür.
     *
     * @param string $key okunacak URI parametresinin adı.
     *
     * @return ?string regex kuralı veya null.
     */
    public function where(string $key): ?string
    {
        return $this->where[$key] ?? null;
    }

    /**
     * URI parametreleri için kullanılacak tüm regex kurallarını döndürür.
     *
     * @return array<string, string> URI parametreleri ve regex kuralları.
     */
    public function allWhere(): array
    {
        return $this->where;
    }

    /**
     * İsteği doğrulamak için kullanılacak schema sınıfını döndürür.
     *
     * @return ?class-string<IRequestSchema> schema sınıfı veya null.
     */
    public function schema(): ?string
    {
        return $this->schema;
    }

    /**
     * Route'un adını döndürür.
     *
     * @return ?string route'un adı veya null.
     */
    public function name(): ?string
    {
        return $this->name;
    }
}
