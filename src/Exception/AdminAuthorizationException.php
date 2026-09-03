<?php
// ============================================================================
// File:    AdminAuthorizationException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;


/**
 * Kullanıcının yönetici yetkisine sahip olmadığı durumlarda oluşan hataları temsil eder.
 */
class AdminAuthorizationException extends RuntimeException
{
    /**
     * Yeni bir admin authorization exception oluşturur.
     *
     * @param string $message hata mesajı.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        string $message = "Administrator authorization is required.",
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, previous: $previous);
    }
}
