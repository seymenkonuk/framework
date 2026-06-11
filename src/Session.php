<?php
// ============================================================================
// File:    Session.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


final class Session
{
    // --------------------------------------------------------------------------
    // CONSTANTS
    // --------------------------------------------------------------------------

    private const FLASH_KEY = "__flash";

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    public function __construct()
    {
        // Güvenlik Ayarları
        session_name("SESSION");
        session_set_cookie_params([
            "secure" => false,
            "httponly" => true,
            "samesite" => "Strict"
        ]);

        // Session Başlatılmadıysa Başlat
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Eski Flash Datasını Temizle
        $this->ageFlashData();
    }

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->getKey($key, $this->all(), $default);
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        /** @var array<string, mixed> $_SESSION */
        return $_SESSION ?? [];
    }

    public function has(string $key): bool
    {
        return $this->hasKey($key, $this->all());
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->remove($key);
        return $value;
    }

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    public function set(string $key, mixed $value): self
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function remove(string $key): self
    {
        unset($_SESSION[$key]);
        return $this;
    }

    public function clear(): self
    {
        $_SESSION = [];
        return $this;
    }

    // --------------------------------------------------------------------------
    // SESSION SECURITY
    // --------------------------------------------------------------------------

    public function regenerate(bool $deleteOldSession = true): self
    {
        session_regenerate_id($deleteOldSession);
        return $this;
    }

    public function id(): string|false
    {
        return session_id();
    }

    // --------------------------------------------------------------------------
    // FLASH
    // --------------------------------------------------------------------------

    public function flash(string $key, mixed $value): self
    {
        // @phpstan-ignore-next-line
        $_SESSION[self::FLASH_KEY]["new"][$key] = $value;
        return $this;
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        // @phpstan-ignore-next-line
        return $this->getKey($key, $_SESSION[self::FLASH_KEY]["old"], $default);
    }

    public function hasFlash(string $key): bool
    {
        // @phpstan-ignore-next-line
        return $this->hasKey($key, $_SESSION[self::FLASH_KEY]["old"]);
    }

    public function removeFlash(string $key): self
    {
        unset(
            // @phpstan-ignore-next-line
            $_SESSION[self::FLASH_KEY]["old"][$key],
            // @phpstan-ignore-next-line
            $_SESSION[self::FLASH_KEY]["new"][$key]
        );

        return $this;
    }

    // --------------------------------------------------------------------------
    // DESTROY
    // --------------------------------------------------------------------------

    public function destroy(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    // --------------------------------------------------------------------------
    // HELPERS
    // --------------------------------------------------------------------------

    /** @param array<string, mixed> $data */
    private function getKey(string $key, array $data, mixed $default = null): mixed
    {
        return $this->hasKey($key, $data)
            ? $data[$key]
            : $default;
    }

    /** @param array<string, mixed> $data */
    private function hasKey(string $key, array $data): bool
    {
        return array_key_exists($key, $data);
    }

    // --------------------------------------------------------------------------
    // INTERNAL
    // --------------------------------------------------------------------------

    private function ageFlashData(): void
    {
        /** @var array<string, mixed> $_SESSION */
        if (!$this->hasKey(self::FLASH_KEY, $_SESSION)) {
            $_SESSION[self::FLASH_KEY] = [];
        }
        // @phpstan-ignore-next-line
        $_SESSION[self::FLASH_KEY]["old"] = $_SESSION[self::FLASH_KEY]["new"] ?? [];
        // @phpstan-ignore-next-line
        $_SESSION[self::FLASH_KEY]["new"] = [];
    }
}
