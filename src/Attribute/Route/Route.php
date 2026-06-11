<?php
// ============================================================================
// File:    Route.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Route;


use Attribute;
use InvalidArgumentException;


#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    /**
     * @param array<string>|string $methods
     * @param string $uri
     */
    public function __construct(
        public array|string $methods,
        public string $uri,
    ) {
        if (is_array($methods) && count($methods) == 0) {
            throw new InvalidArgumentException(
                'At least one HTTP method is required.'
            );
        }
    }
}
