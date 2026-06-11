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
    public function __construct(
        public string $uri,
    ) {
        parent::__construct(["PUT"], $uri);
    }
}
