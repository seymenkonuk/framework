<?php
// ============================================================================
// File:    ValidationException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;


/**
 * Veri doğrulaması başarısız olduğunda oluşan hatayı temsil eder.
 */
class ValidationException extends RuntimeException
{
    /**
     * Yeni bir validation exception oluşturur.
     *
     * @param array<string, mixed> $errors doğrulama sırasında oluşan hatalar.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        protected readonly array $errors,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "Validation failed.",
            previous: $previous,
        );
    }

    /**
     * Doğrulama hatalarını döndürür.
     *
     * @return array<string, mixed> doğrulama hataları.
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
