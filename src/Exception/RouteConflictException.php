<?php
// ============================================================================
// File:    RouteConflictException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use Exception;
use Throwable;


class RouteConflictException extends Exception
{
    public function __construct(
        string $method,
        string $uri,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "Route conflict detected: [$method] $uri",
            previous: $previous,
        );
    }
}
