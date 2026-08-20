<?php
// ============================================================================
// File:    Schema.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute;


use Attribute;

use Seymenkonuk\Framework\Http\RequestSchema\IRequestSchema;


#[Attribute(Attribute::TARGET_METHOD)]
class Schema
{
    /**
     * Route için kullanılacak request schema sınıfını tanımlar.
     *
     * Belirtilen schema sınıfı, route'a gelen request verilerinin doğrulanması
     * için kullanılır.
     *
     * @param class-string<IRequestSchema> $schema kullanılacak request schema sınıfı.
     */
    public function __construct(
        public readonly string $schema,
    ) {}
}
