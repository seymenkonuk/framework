<?php
// ============================================================================
// File:    Name.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute;


use Attribute;

use Seymenkonuk\Framework\Routing\Route;


#[Attribute(Attribute::TARGET_METHOD)]
class Name implements IRouteModifier
{
    /**
     * Route için benzersiz bir isim tanımlar.
     *
     * Tanımlanan isim, route'a daha sonra isim üzerinden erişilmesini sağlar.
     *
     * @param string $name route için kullanılacak isim.
     */
    public function __construct(
        public readonly string $name,
    ) {}

    public function apply(Route $route): void
    {
        $route->name($this->name);
    }
}
