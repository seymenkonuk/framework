<?php
// ============================================================================
// File:    IFlash.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Flash;


interface IFlash
{
    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    public function get(string $key, mixed $default = null): mixed;
    public function has(string $key): bool;

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    public function set(string $key, mixed $value): self;
    public function remove(string $key): self;
    public function clear(): self;

    // --------------------------------------------------------------------------
    // LIFECYCLE
    // --------------------------------------------------------------------------

    public function age(): self;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string;
}
