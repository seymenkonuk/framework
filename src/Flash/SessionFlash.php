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

    private const OLD_FLASH_PARENT_KEY = "__flash_old";
    private const NEW_FLASH_PARENT_KEY = "__flash_new";

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
        return $this->session->get($key, $default, self::OLD_FLASH_PARENT_KEY);
    }

    public function has(string $key): bool
    {
        return $this->session->has($key, self::OLD_FLASH_PARENT_KEY);
    }

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    public function set(string $key, mixed $value): bool
    {
        return $this->session->set($key, $value, self::NEW_FLASH_PARENT_KEY);
    }

    public function remove(string $key): bool
    {
        $oldRemoved = $this->session->remove($key, self::OLD_FLASH_PARENT_KEY);
        $newRemoved = $this->session->remove($key, self::NEW_FLASH_PARENT_KEY);
        return $oldRemoved || $newRemoved;
    }

    public function clear(): bool
    {
        $oldCleared = $this->session->clear(self::OLD_FLASH_PARENT_KEY);
        $newCleared = $this->session->clear(self::NEW_FLASH_PARENT_KEY);
        return $oldCleared && $newCleared;
    }

    // --------------------------------------------------------------------------
    // LIFECYCLE
    // --------------------------------------------------------------------------

    public function age(): void
    {
        $new = $this->session->all(self::NEW_FLASH_PARENT_KEY);
        $this->session->replace($new, self::OLD_FLASH_PARENT_KEY);
        $this->session->replace([], self::NEW_FLASH_PARENT_KEY);
    }

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string
    {
        return "session";
    }
}
