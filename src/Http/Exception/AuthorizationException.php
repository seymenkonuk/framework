<?php
// ============================================================================
// File:    AuthorizationException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Exception;


use RuntimeException;
use Throwable;


/**
 * İstenen kaynağa erişim yetkisi bulunmadığında oluşan hatayı temsil eder.
 */
class AuthorizationException extends RuntimeException
{
    /**
     * Yeni bir authorization exception oluşturur.
     *
     * @param string $title hata başlığı.
     * @param string $description hatanın açıklaması.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        protected readonly string $title = "Unauthorized",
        protected readonly string $description = "You are not authorized to access this resource.",
        ?Throwable $previous = null,
    ) {
        parent::__construct($description, previous: $previous);
    }

    /**
     * Hatanın başlığını döndürür.
     *
     * @return string hata başlığı.
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Hatanın açıklamasını döndürür.
     *
     * @return string hata açıklaması.
     */
    public function description(): string
    {
        return $this->description;
    }
}
