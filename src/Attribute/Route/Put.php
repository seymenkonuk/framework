<?php
// ============================================================================
// File:    Put.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Route;


use Attribute;


#[Attribute(Attribute::TARGET_METHOD)]
class Put extends Route
{
    /**
     * HTTP PUT metodu için bir route tanımlar.
     * 
     * @param string $uri route'un eşleşeceği URI.
     */
    public function __construct(
        string $uri,
    ) {
        parent::__construct("PUT", $uri);
    }
}
