<?php
// ============================================================================
// File:    Query.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Route;


use Attribute;


#[Attribute(Attribute::TARGET_METHOD)]
class Query extends Route
{
    public function __construct(
        public string $uri,
    ) {
        parent::__construct(["QUERY"], $uri);
    }
}
