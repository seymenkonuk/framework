<?php
// ============================================================================
// File:    IAuthService.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Auth;


interface IAuthService
{
    // --------------------------------------------------------------------------
    // AUTHENTICATION
    // --------------------------------------------------------------------------

    /**
     * Kullanıcının kimliğinin doğrulanmış olup olmadığını döndürür.
     *
     * @return bool kullanıcı giriş yapmışsa true, aksi halde false.
     */
    public function authenticated(): bool;
}
