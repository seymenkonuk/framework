<?php
// ============================================================================
// File:    RouteNotFoundException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use Exception;
use Throwable;


class RouteNotFoundException extends Exception
{
    public function __construct(
        string $method = '',
        string $uri = '',
        ?Throwable $previous = null,
    ) {
        $message = 'Route not found';

        if ($method !== '' || $uri !== '') {
            $message .= ": [$method] $uri";
        }

        parent::__construct($message, 404, previous: $previous);
    }
}
