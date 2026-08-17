<?php
// ============================================================================
// File:    RouteConfig.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Routing;


abstract class RouteConfig
{
    /**
     * Route yapılandırmalarını router'a kaydeder.
     *
     * @param Router $router route'ların kaydedileceği router.
     *
     * @return void
     */
    abstract public function register(Router $router): void;
}
