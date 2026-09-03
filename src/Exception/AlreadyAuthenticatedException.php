<?php
// ============================================================================
// File:    AlreadyAuthenticatedException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;


/**
 * Zaten kimliği doğrulanmış kullanıcıların gerçekleştirememesi gereken
 * işlemler sırasında oluşan hataları temsil eder.
 */
class AlreadyAuthenticatedException extends RuntimeException
{
    /**
     * Yeni bir already authenticated exception oluşturur.
     *
     * @param string $message hata mesajı.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        string $message = "The user is already authenticated.",
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, previous: $previous);
    }
}
