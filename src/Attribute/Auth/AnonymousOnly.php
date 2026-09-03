<?php
// ============================================================================
// File:    AnonymousOnly.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Attribute\Auth;


use Attribute;

use Seymenkonuk\Framework\Http\Middleware\AnonymousOnlyMiddleware;


#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class AnonymousOnly extends Authenticate
{
    /**
     * Route'un yalnızca kimliği doğrulanmamış kullanıcılar tarafından
     * erişilebileceğini belirtir.
     *
     * Sınıf seviyesinde tanımlandığında sınıf içerisindeki tüm route'lara
     * Metot seviyesinde tanımlandığında yalnızca ilgili route'a uygulanır.
     *
     * Sınıf ve metot seviyesinde farklı bir authentication kuralı tanımlanmışsa
     * metot seviyesinde tanımlanan kural geçerli olur.
     */
    public function __construct()
    {
        parent::__construct(AnonymousOnlyMiddleware::class);
    }
}
