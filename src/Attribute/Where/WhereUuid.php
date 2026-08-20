<?php
// ============================================================================
// File:    WhereUuid.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Where;


use Attribute;


#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class WhereUuid extends Where
{
    /**
     * Route parametresinin geçerli bir UUID değerine uygun olmasını gerektiren
     * bir eşleşme kuralı tanımlar.
     * 
     * @param string $key route parametresinin adı.
     */
    public function __construct(
        string $key,
    ) {
        parent::__construct($key, "[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}");
    }
}
