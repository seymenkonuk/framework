<?php
// ============================================================================
// File:    SessionFlash.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Flash;


use Seymenkonuk\Framework\Session\ISession;


final class SessionFlash implements IFlash
{
    // --------------------------------------------------------------------------
    // CONSTANTS
    // --------------------------------------------------------------------------

    private const FLASH_PARENT_KEYS = ["__flash"];
    private const OLD_FLASH_PARENT_KEYS = [...self::FLASH_PARENT_KEYS, "old"];
    private const NEW_FLASH_PARENT_KEYS = [...self::FLASH_PARENT_KEYS, "new"];

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    public function __construct(
        protected ISession $session,
    ) {}

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->session->get($key, $default, self::OLD_FLASH_PARENT_KEYS);
    }

    public function has(string $key): bool
    {
        return $this->session->has($key, self::OLD_FLASH_PARENT_KEYS);
    }

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    public function set(string $key, mixed $value): bool
    {
        return $this->session->set($key, $value, self::NEW_FLASH_PARENT_KEYS);
    }

    public function remove(string $key): bool
    {
        $oldRemoved = $this->session->remove($key, self::OLD_FLASH_PARENT_KEYS);
        $newRemoved = $this->session->remove($key, self::NEW_FLASH_PARENT_KEYS);
        return $oldRemoved || $newRemoved;
    }

    public function clear(): bool
    {
        $oldCleared = $this->session->clear(self::OLD_FLASH_PARENT_KEYS);
        $newCleared = $this->session->clear(self::NEW_FLASH_PARENT_KEYS);
        return $oldCleared && $newCleared;
    }

    // --------------------------------------------------------------------------
    // LIFECYCLE
    // --------------------------------------------------------------------------

    public function age(): void
    {
        $new = $this->session->get("new", [], self::FLASH_PARENT_KEYS);
        $this->session->set("old", $new, self::FLASH_PARENT_KEYS);
        $this->session->set("new", [], self::FLASH_PARENT_KEYS);
    }

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string
    {
        return "session";
    }
}
