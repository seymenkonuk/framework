<?php
// ============================================================================
// File:    DatabaseException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;


/**
 * Veritabanı işlemleri sırasında oluşan hataları temsil eder.
 */
class DatabaseException extends RuntimeException
{
    /**
     * Yeni bir database exception oluşturur.
     *
     * @param string $message hata mesajı.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        string $message = "A database error occurred.",
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, previous: $previous);
    }
}
