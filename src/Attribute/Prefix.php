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
    /**
     * Sınıf içerisindeki tüm route'lara uygulanacak URI ön ekini tanımlar.
     *
     * Sınıf içerisindeki tüm route'ların URI'larının başına belirtilen değer
     * eklenir.
     *
     * @param string $uri route'lara uygulanacak URI ön eki.
     */
    public function __construct(
        public string $uri,
    ) {}
}
