<?php
// ============================================================================
// File:    Where.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Where;


use Attribute;


#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Where
{
    public function __construct(
        public string $key,
        public string $pattern,
    ) {}
}
