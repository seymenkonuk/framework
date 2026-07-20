<?php
// ============================================================================
// File:    CsrfToken.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


final class CsrfToken
{
    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    public function __construct(
        protected Session $session
    ) {}

    // --------------------------------------------------------------------------
    //  METHODS
    // --------------------------------------------------------------------------

    /** @return ?array{value: string, expires: int} */
    private function get(): ?array
    {
        /** @var ?array{value: string, expires: int} $token */
        $token = $this->session->get("csrfToken", null);
        return $token;
    }

    public function getToken(): ?string
    {
        $token = $this->get();
        if ($token === null) {
            return $token;
        }
        return $token["value"];
    }

    public function set(string $value, int $expires = 60 * 60): void
    {
        $this->session->set("csrfToken", [
            "value" => $value,
            "expires" => time() + $expires,
        ]);
    }

    public function revoke(): void
    {
        $this->session->remove("csrfToken");
    }

    public function refresh(): ?string
    {
        $this->set(bin2hex(random_bytes(32)));
        return $this->getToken();
    }

    public function hasExpired(): bool
    {
        $csrfToken = $this->get();

        if ($csrfToken === null) {
            return true;
        }

        return time() > $csrfToken["expires"];
    }

    public function isValid(?string $token): bool
    {
        $csrfToken = $this->getToken();

        if ($token === null || $csrfToken === null) {
            return false;
        }

        if ($this->hasExpired()) {
            return false;
        }

        return hash_equals($csrfToken, $token);
    }
}
