<?php
// ============================================================================
// File:    WhereHash.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Where;


use Attribute;


#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class WhereHash extends Where
{
    /**
     * Route parametresinin hexadecimal karakterlerden oluşan bir hash değerine
     * uygun olmasını gerektiren bir eşleşme kuralı tanımlar.
     * 
     * @param string $key route parametresinin adı.
     */
    public function __construct(
        string $key,
    ) {
        parent::__construct($key, "[a-fA-F0-9]+");
    }
}
