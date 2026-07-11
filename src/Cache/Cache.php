<?php
// ============================================================================
// File:    Cache.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Cache;


interface Cache
{
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value, int $ttl = 0): bool;

    public function has(string $key): bool;
    public function forget(string $key): bool;

    public function clear(): bool;

    // --------------------------------------------------------------------------
    // PULL
    // --------------------------------------------------------------------------

    public function pull(string $key, mixed $default = null): mixed;

    // --------------------------------------------------------------------------
    // INCREMENT / DECREMENT
    // --------------------------------------------------------------------------

    public function increment(string $key, int $value = 1): int;
    public function decrement(string $key, int $value = 1): int;

    // --------------------------------------------------------------------------
    // MULTI
    // --------------------------------------------------------------------------

    /**
     * @param array<string> $keys
     * @param mixed $default
     * @return array<string, mixed>
     */
    public function getMultiple(array $keys, mixed $default = null): array;

    /** @param array<string, mixed> $values */
    public function setMultiple(array $values, int $ttl = 0): bool;

    /** @param array<string> $keys */
    public function forgetMultiple(array $keys): bool;

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
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string;
    public function flush(): bool;
}
