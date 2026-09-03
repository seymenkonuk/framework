<?php
// ============================================================================
// File:    Middleware.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute;


use Attribute;

use Seymenkonuk\Framework\Http\Middleware as HttpMiddleware;

use Seymenkonuk\Framework\Routing\Route;


#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Middleware implements IRouteModifier
{
    /**
     * Route'a uygulanacak middleware sınıfını tanımlar.
     *
     * Sınıf seviyesinde tanımlandığında sınıf içerisindeki tüm route'lara,
     * metot seviyesinde tanımlandığında yalnızca ilgili route'a uygulanır.
     *
     * Aynı seviyede birden fazla middleware tanımlanabilir.
     *
     * @param class-string<HttpMiddleware> $middleware uygulanacak middleware sınıfı.
     */
    public function __construct(
        public readonly string $middleware,
    ) {}

    public function apply(Route $route): void
    {
        $route->middleware($this->middleware);
    }
}
