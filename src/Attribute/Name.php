<?php
// ============================================================================
// File:    Name.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute;


use Attribute;


#[Attribute(Attribute::TARGET_METHOD)]
class Name
{
    /**
     * Route için benzersiz bir isim tanımlar.
     *
     * Tanımlanan isim, route'a daha sonra isim üzerinden erişilmesini sağlar.
     *
     * @param string $name route için kullanılacak isim.
     */
    public function __construct(
        public readonly string $name,
    ) {}
}
