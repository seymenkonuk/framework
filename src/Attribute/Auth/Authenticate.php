<?php
// ============================================================================
// File:    Authenticate.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Auth;


use Attribute;

use Seymenkonuk\Framework\Attribute\IRouteModifier;

use Seymenkonuk\Framework\Http\Middleware;

use Seymenkonuk\Framework\Routing\Route;


#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Authenticate implements IRouteModifier
{
    /**
     * Route için kullanılacak authentication middleware'ini belirtir.
     *
     * Sınıf seviyesinde tanımlandığında sınıf içerisindeki tüm route'lara,
     * metot seviyesinde tanımlandığında yalnızca ilgili route'a uygulanır.
     *
     * Sınıf ve metot seviyesinde farklı bir authentication kuralı tanımlanmışsa
     * metot seviyesinde tanımlanan kural geçerli olur.
     *
     * @param class-string<Middleware> $middleware authentication middleware sınıfı.
     */
    public function __construct(
        public readonly string $middleware,
    ) {}

    public function apply(Route $route): void
    {
        $route->authenticate($this->middleware);
    }
}
