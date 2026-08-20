<?php
// ============================================================================
// File:    RouteConflictException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;


/**
 * Route tanımları arasında çakışma oluştuğunda meydana gelen hatayı temsil eder.
 */
class RouteConflictException extends RuntimeException
{
    /**
     * Yeni bir route conflict exception oluşturur.
     *
     * @param string $method çakışan route'un HTTP metodu.
     * @param string $uri çakışan route'un URI deseni.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        protected readonly string $method,
        protected readonly string $uri,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "Route conflict detected: [{$method}] {$uri}",
            previous: $previous,
        );
    }

    /**
     * Çakışan route'un HTTP metodunu döndürür.
     *
     * @return string HTTP metodu.
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Çakışan route'un URI desenini döndürür.
     *
     * @return string URI deseni.
     */
    public function uri(): string
    {
        return $this->uri;
    }
}
