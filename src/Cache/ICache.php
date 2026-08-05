<?php
// ============================================================================
// File:    ICache.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Cache;


interface ICache
{
    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    public function get(string $key, mixed $default = null): mixed;
    public function has(string $key): bool;
    public function pull(string $key, mixed $default = null): mixed;

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    public function set(string $key, mixed $value, int $ttl = 0): bool;
    public function remove(string $key): bool;
    public function clear(): bool;

    // --------------------------------------------------------------------------
    // INCREMENT / DECREMENT
    // --------------------------------------------------------------------------

    public function increment(string $key, int $value = 1): int;
    public function decrement(string $key, int $value = 1): int;

    // --------------------------------------------------------------------------
    // MULTI
    // --------------------------------------------------------------------------

    public function getMultiple(array $keys, mixed $default = null): array;
    public function setMultiple(array $values, int $ttl = 0): bool;
    public function removeMultiple(array $keys): bool;

    // --------------------------------------------------------------------------
    // TTL
    // --------------------------------------------------------------------------

    public function ttl(string $key): int;
    public function expire(string $key, int $ttl): bool;

    // --------------------------------------------------------------------------
    // LOCK
    // --------------------------------------------------------------------------

    public function lock(string $key, int $ttl = 10): bool;
    public function unlock(string $key): bool;
    public function isLocked(string $key): bool;

    // --------------------------------------------------------------------------
    // REMEMBER
    // --------------------------------------------------------------------------

    public function remember(string $key, int $ttl, callable $callback): mixed;

    // --------------------------------------------------------------------------
    // DESTROY
    // --------------------------------------------------------------------------

    public function destroy(): bool;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string;
}
