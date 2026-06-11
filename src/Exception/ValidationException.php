<?php
// ============================================================================
// File:    ValidationException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use Exception;
use Throwable;


class ValidationException extends Exception
{
    /** @param array<string, mixed>|string $errors */
    public function __construct(
        protected array|string $errors,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Doğrulama Hatası", previous: $previous);
    }

    /** @return array<string, mixed>|string */
    public function errors(): array|string
    {
        return $this->errors;
    }
}
