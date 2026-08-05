<?php
// ============================================================================
// File:    ISession.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Session;


interface ISession
{
    public const DEFAULT_PARENT_KEYS = ["__data"];

    // --------------------------------------------------------------------------
    // SESSION IDENTITY
    // --------------------------------------------------------------------------

    public function id(): string|false;
    public function regenerate(bool $deleteOldSession = true): self;

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    /**
     * @param array<string> $parentKeys
     * @return array<string, mixed>
     */
    public function all(array $parentKeys = self::DEFAULT_PARENT_KEYS): array;
    /** @param array<string> $parentKeys */
    public function get(string $key, mixed $default = null, array $parentKeys = self::DEFAULT_PARENT_KEYS): mixed;
    /** @param array<string> $parentKeys */
    public function has(string $key, array $parentKeys = self::DEFAULT_PARENT_KEYS): bool;
    /** @param array<string> $parentKeys */
    public function pull(string $key, mixed $default = null, array $parentKeys = self::DEFAULT_PARENT_KEYS): mixed;

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    /** @param array<string> $parentKeys */
    public function set(string $key, mixed $value, array $parentKeys = self::DEFAULT_PARENT_KEYS): self;
    /** @param array<string> $parentKeys */
    public function remove(string $key, array $parentKeys = self::DEFAULT_PARENT_KEYS): self;
    /** @param array<string> $parentKeys */
    public function clear(array $parentKeys = self::DEFAULT_PARENT_KEYS): self;

    // --------------------------------------------------------------------------
    // DESTROY
    // --------------------------------------------------------------------------

    public function destroy(): void;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string;
}
