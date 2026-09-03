<?php
// ============================================================================
// File:    IRouteModifier.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute;


use Seymenkonuk\Framework\Routing\Route;


interface IRouteModifier
{
    /**
     * Route üzerinde değişiklik yapar.
     *
     * @param Route $route Değiştirilecek route.
     *
     * @return void
     */
    public function apply(Route $route): void;
}
