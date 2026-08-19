<?php
// ============================================================================
// File:    Route.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Route;


use Attribute;
use InvalidArgumentException;


#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    /**
     * Bir veya birden fazla HTTP metodu için route tanımlar.
     * 
     * $methods değeri string olarak verilirse route yalnızca belirtilen HTTP
     * metodu ile eşleşir. Array olarak verilirse route, belirtilen HTTP
     * metotlarından herhangi biriyle eşleşir.
     * 
     * Boş bir HTTP metotları dizisi verilmesi durumunda InvalidArgumentException
     * fırlatılır.
     * 
     * @param array<string>|string $methods route'un eşleşeceği HTTP metodu veya metotları.
     * @param string $uri route'un eşleşeceği URI.
     * 
     * @throws InvalidArgumentException $methods boş bir array ise.
     */
    public function __construct(
        public readonly array|string $methods,
        public readonly string $uri,
    ) {
        if (is_array($methods) && count($methods) == 0) {
            throw new InvalidArgumentException(
                'At least one HTTP method is required.'
            );
        }
    }
}
