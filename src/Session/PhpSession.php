<?php
// ============================================================================
// File:    PhpSession.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Session;


final class PhpSession implements ISession
{
    // --------------------------------------------------------------------------
    // CONSTANTS
    // --------------------------------------------------------------------------

    private const SESSION_NAME = 'SESSION';

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Session İsmi
            session_name(self::SESSION_NAME);
            // Güvenlik Ayarları
            session_set_cookie_params([
                "secure" => false,
                "httponly" => true,
                "samesite" => "Strict"
            ]);
            // Session Başlatılmadıysa Başlat
            session_start();
        }
    }

    // --------------------------------------------------------------------------
    // SESSION IDENTITY
    // --------------------------------------------------------------------------

    public function id(): string|false
    {
        return session_id();
    }

    public function regenerate(bool $deleteOldSession = true): bool
    {
        return session_regenerate_id($deleteOldSession);
    }

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    public function all(string $parentKey = self::DEFAULT_PARENT_KEY): array
    {
        // Parent Key Bulunamadı veya Hatalı
        if (!array_key_exists($parentKey, $_SESSION) || !is_array($_SESSION[$parentKey])) {
            return [];
        }
        // Değeri Döndür
        // @phpstan-ignore-next-line return.type
        return $_SESSION[$parentKey];
    }

    public function get(string $key, mixed $default = null, string $parentKey = self::DEFAULT_PARENT_KEY): mixed
    {
        // Key Mevcut Değil
        if (!$this->has($key, $parentKey)) {
            return $default;
        }
        // Değeri Döndür
        $all = $this->all($parentKey);
        return $all[$key];
    }

    public function has(string $key, string $parentKey = self::DEFAULT_PARENT_KEY): bool
    {
        $all = $this->all($parentKey);
        return array_key_exists($key, $all);
    }

    public function pull(string $key, mixed $default = null, string $parentKey = self::DEFAULT_PARENT_KEY): mixed
    {
        $result = $this->get($key, $default, $parentKey);
        $this->remove($key, $parentKey);
        return $result;
    }

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    public function replace(array $data, string $parentKey = self::DEFAULT_PARENT_KEY): bool
    {
        $_SESSION[$parentKey] = $data;
        return true;
    }

    public function set(string $key, mixed $value, string $parentKey = self::DEFAULT_PARENT_KEY): bool
    {
        // Parent Key Bulunamadı veya Hatalı
        if (!array_key_exists($parentKey, $_SESSION) || !is_array($_SESSION[$parentKey])) {
            $_SESSION[$parentKey] = [];
        }
        // Değeri Kaydet
        $_SESSION[$parentKey][$key] = $value;
        return true;
    }

    public function remove(string $key, string $parentKey = self::DEFAULT_PARENT_KEY): bool
    {
        // Key Mevcut Değil
        if (!$this->has($key, $parentKey)) {
            return true;
        }
        // Key Silindi
        // @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible
        unset($_SESSION[$parentKey][$key]);
        return true;
    }

    public function clear(string $parentKey = self::DEFAULT_PARENT_KEY): bool
    {
        // Parent Key Bulunamadı
        if (!array_key_exists($parentKey, $_SESSION)) {
            return true;
        }
        // Parent Key Silindi
        unset($_SESSION[$parentKey]);
        return true;
    }

    // --------------------------------------------------------------------------
    // DESTROY
    // --------------------------------------------------------------------------

    public function destroy(): void
    {
        session_destroy();
    }

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string
    {
        return 'php';
    }
}
