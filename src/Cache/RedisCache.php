<?php
// ============================================================================
// File:    RedisCache.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Cache;


use Predis\Client as Predis;


final class RedisCache implements ICache
{
    // --------------------------------------------------------------------------
    // CONSTANTS
    // --------------------------------------------------------------------------

    private const PREFIX = "cache:";

    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    protected Predis $redis;

    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    public function __construct(
        string $host,
        string $port,
        ?string $password = null,
        int $databaseId = 0,
    ) {
        $parameters = [
            'scheme'   => 'tcp',
            'host'     => $host,
            'port'     => $port,
            'database' => $databaseId,
        ];
        if ($password !== null) {
            $parameters["password"] = $password;
        }
        $this->redis = new Predis($parameters);
    }

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->key($key);
        $value = $this->redis->get($key);

        return $value !== null
            ? unserialize($value)
            : $default;
    }

    public function has(string $key): bool
    {
        $key = $this->key($key);
        return (bool) $this->redis->exists($key);
    }

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        $key = $this->key($key);
        $value = serialize($value);

        if ($ttl > 0) {
            return $this->redis->set($key, $value, "EX", $ttl) == true;
        }

        return $this->redis->set($key, $value) == true;
    }

    public function remove(string $key): bool
    {
        $key = $this->key($key);
        return (bool) $this->redis->del($key);
    }

    public function clear(): bool
    {
        $pattern = $this->key("*");
        /** @var array<string> $keys */
        $keys = $this->redis->keys($pattern);

        if (empty($keys)) {
            return true;
        }

        return (bool) $this->redis->del($keys);
    }

    // --------------------------------------------------------------------------
    // PULL
    // --------------------------------------------------------------------------

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->remove($key);
        return $value;
    }

    // --------------------------------------------------------------------------
    // INCREMENT / DECREMENT
    // --------------------------------------------------------------------------

    public function increment(string $key, int $value = 1): int
    {
        $key = $this->key($key);
        return $this->redis->incrby($key, $value);
    }

    public function decrement(string $key, int $value = 1): int
    {
        $key = $this->key($key);
        return $this->redis->decrby($key, $value);
    }

    // --------------------------------------------------------------------------
    // MULTI
    // --------------------------------------------------------------------------

    public function getMultiple(array $keys, mixed $default = null): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    public function setMultiple(array $values, int $ttl = 0): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            $result2 = $this->set($key, $value, $ttl);
            $result = $result && $result2;
        }
        return $result;
    }

    public function removeMultiple(array $keys): bool
    {
        $prefixed = array_map(fn($key) => $this->key($key), $keys);
        return (bool) $this->redis->del($prefixed);
    }

    // --------------------------------------------------------------------------
    // TTL
    // --------------------------------------------------------------------------

    public function ttl(string $key): int
    {
        $key = $this->key($key);
        return $this->redis->ttl($key);
    }

    public function expire(string $key, int $ttl): bool
    {
        $key = $this->key($key);
        return (bool) $this->redis->expire($key, $ttl);
    }

    // --------------------------------------------------------------------------
    // LOCK
    // --------------------------------------------------------------------------

    public function lock(string $key, int $ttl = 10): bool
    {
        $key = $this->key("lock:$key");
        return $this->redis->set($key, "1", "EX", $ttl, "NX") == true;
    }

    public function unlock(string $key): bool
    {
        return $this->remove("lock:$key");
    }

    public function isLocked(string $key): bool
    {
        return $this->has("lock:$key");
    }

    // --------------------------------------------------------------------------
    // REMEMBER
    // --------------------------------------------------------------------------

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        // Cache'de Varsa Onu Getir
        if ($this->has($key)) {
            return $this->get($key);
        }

        // Kilitliyse Callback'i Çağırma Cache'den Getir (Yoksa Bile)
        if ($this->isLocked($key)) {
            return $this->get($key);
        }

        // Kilidi Al
        if ($this->lock($key)) {
            try {
                // Veriyi Callback'den Al ve Cache'e Yaz
                $value = $callback();
                $this->set($key, $value, $ttl);
            } finally {
                $this->unlock($key);
            }

            return $value;
        }

        // Kilidi Alamadıysan (Race Condition) Tekrar Cache Dene
        return $this->get($key);
    }

    // --------------------------------------------------------------------------
    // DESTROY
    // --------------------------------------------------------------------------

    public function destroy(): bool
    {
        return $this->redis->flushdb() === "OK";
    }

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string
    {
        return "redis (predis)";
    }

    // --------------------------------------------------------------------------
    // INTERNAL
    // --------------------------------------------------------------------------

    private function key(string $key): string
    {
        return self::PREFIX . $key;
    }
}
