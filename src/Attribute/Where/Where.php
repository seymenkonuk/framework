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
    /**
     * Route parametrelerinden biri için eşleşme kuralı tanımlar.
     *
     * @param string $key route parametresinin adı.
     * @param string $pattern route parametresinin eşleşeceği düzenli ifade (regex).
     */
    public function __construct(
        public readonly string $key,
        public readonly string $pattern,
    ) {}
}
