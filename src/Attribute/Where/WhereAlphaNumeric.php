<?php
// ============================================================================
// File:    WhereAlphaNumeric.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Where;


use Attribute;


#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class WhereAlphaNumeric extends Where
{
    /**
     * Route parametresinin yalnızca alfabetik ve sayısal karakterlerden oluşmasını
     * gerektiren bir eşleşme kuralı tanımlar.
     * 
     * @param string $key route parametresinin adı.
     */
    public function __construct(
        public string $key,
    ) {
        parent::__construct($key, "[a-zA-Z0-9]+");
    }
}
