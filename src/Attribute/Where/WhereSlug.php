<?php
// ============================================================================
// File:    WhereSlug.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Where;


use Attribute;


#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class WhereSlug extends Where
{
    /**
     * Route parametresinin geçerli bir slug değerine uygun olmasını gerektiren
     * bir eşleşme kuralı tanımlar.
     * 
     * @param string $key route parametresinin adı.
     */
    public function __construct(
        public string $key,
    ) {
        parent::__construct($key, "[a-zA-Z0-9\-]+");
    }
}
