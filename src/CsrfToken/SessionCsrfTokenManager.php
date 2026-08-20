<?php
// ============================================================================
// File:    SessionCsrfTokenManager.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\CsrfToken;


use Seymenkonuk\Framework\Session\ISession;


final class SessionCsrfTokenManager implements ICsrfTokenManager
{
    // --------------------------------------------------------------------------
    // CONSTANTS
    // --------------------------------------------------------------------------

    private const PARENT_KEY = "__csrf_token";

    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    public function __construct(
        protected ISession $session
    ) {}

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    public function get(): string|false
    {
        // Token Bulunamadı
        if (!$this->has()) {
            return false;
        }
        // Token Değerini Döndür
        return $this->session->get("value", "", self::PARENT_KEY);
    }

    public function has(): bool
    {
        return $this->session->has("value", self::PARENT_KEY);
    }

    // --------------------------------------------------------------------------
    // SETTERS
    // --------------------------------------------------------------------------

    public function set(string $value, int $expires = self::DEFAULT_TTL): void
    {
        $this->session->set("value", $value, self::PARENT_KEY);
        $this->session->set("expires", time() + $expires, self::PARENT_KEY);
    }

    public function refresh(int $expires = self::DEFAULT_TTL): string
    {
        $value = bin2hex(random_bytes(32));
        $this->set($value, $expires);
        return $value;
    }

    public function revoke(): void
    {
        $this->session->clear(self::PARENT_KEY);
    }

    // --------------------------------------------------------------------------
    // VALIDATION
    // --------------------------------------------------------------------------

    public function expired(): bool
    {
        // Süresini Al
        $expires = $this->session->get("expires", time() - 1, self::PARENT_KEY);
        // Süresini Karşılaştır
        return time() > $expires;
    }

    public function valid(?string $token): bool
    {
        // Geçersiz Token
        if ($token === null) {
            return false;
        }
        // Token Yoksa veya Süresi Dolduysa
        if (!$this->has() || $this->expired()) {
            return false;
        }
        // Tokenları Karşılaştır
        $csrfToken = $this->get() ?: "";
        return hash_equals($csrfToken, $token);
    }

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string
    {
        return "session";
    }
}
