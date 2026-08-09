<?php
// ============================================================================
// File:    Any.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Route;


use Attribute;


#[Attribute(Attribute::TARGET_METHOD)]
class Any extends Route
{
    /**
     * Desteklenen tüm HTTP metotları için bir route tanımlar.
     * 
     * @param string $uri route'un eşleşeceği URI.
     */
    public function __construct(
        public string $uri,
    ) {
        parent::__construct(["GET", "QUERY", "POST", "PATCH", "PUT", "DELETE"], $uri);
    }
}
