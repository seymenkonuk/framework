<?php
// ============================================================================
// File:    Prefix.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute;


use Attribute;


#[Attribute(Attribute::TARGET_CLASS)]
class Prefix
{
    public function __construct(
        public string $uri,
    ) {}
}
