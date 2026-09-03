<?php
// ============================================================================
// File:    IAdminAuthService.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Auth;


interface IAdminAuthService extends IAuthService
{
    // --------------------------------------------------------------------------
    // AUTHORIZATION
    // --------------------------------------------------------------------------

    /**
     * Kullanıcının yönetici yetkisine sahip olup olmadığını döndürür.
     *
     * @return bool kullanıcı yönetici ise true, aksi halde false.
     */
    public function isAdmin(): bool;
}
