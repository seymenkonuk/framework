<?php
// ============================================================================
// File:    TemplateException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;


/**
 * Template işlemleri sırasında oluşan hatayı temsil eder.
 */
class TemplateException extends RuntimeException
{
    /**
     * Yeni bir template exception oluşturur.
     *
     * @param string $message hata mesajı.
     * @param ?string $template hataya neden olan template veya null.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        string $message = "A template error occurred.",
        protected readonly ?string $template = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, previous: $previous);
    }

    /**
     * Hataya neden olan template'i döndürür.
     *
     * @return ?string template adı veya null.
     */
    public function template(): ?string
    {
        return $this->template;
    }
}
