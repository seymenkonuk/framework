<?php
// ============================================================================
// File:    ICsrfTokenManager.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\CsrfToken;


interface ICsrfTokenManager
{
    public const DEFAULT_TTL = 60 * 60;

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    public function get(): string|false;
    public function has(): bool;

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    public function set(string $value, int $expires = self::DEFAULT_TTL): void;
    public function refresh(int $expires = self::DEFAULT_TTL): string;
    public function revoke(): void;

    // --------------------------------------------------------------------------
    // VALIDATION
    // --------------------------------------------------------------------------

    public function expired(): bool;
    public function valid(?string $token): bool;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string;
}
