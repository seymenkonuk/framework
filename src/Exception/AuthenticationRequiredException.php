<?php
// ============================================================================
// File:    AuthenticationRequiredException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;


/**
 * Kimlik doğrulamanın gerekli olduğu durumlarda oluşan hataları temsil eder.
 */
class AuthenticationRequiredException extends RuntimeException
{
    /**
     * Yeni bir authentication required exception oluşturur.
     *
     * @param string $message hata mesajı.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        string $message = "Authentication is required.",
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, previous: $previous);
    }
}
