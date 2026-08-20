<?php
// ============================================================================
// File:    FileNotFoundException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;


/**
 * İstenen dosya bulunamadığında oluşan hatayı temsil eder.
 */
class FileNotFoundException extends RuntimeException
{
    /**
     * Yeni bir file not found exception oluşturur.
     *
     * @param string $path bulunamayan dosyanın yolu.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        protected readonly string $path,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "File not found: {$path}",
            previous: $previous,
        );
    }

    /**
     * Bulunamayan dosyanın yolunu döndürür.
     *
     * @return string dosya yolu.
     */
    public function path(): string
    {
        return $this->path;
    }
}
