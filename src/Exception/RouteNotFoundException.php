<?php
// ============================================================================
// File:    RouteNotFoundException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;

/**
 * İstenen route bulunamadığında oluşan hatayı temsil eder.
 */
class RouteNotFoundException extends RuntimeException
{
    /**
     * Yeni bir route not found exception oluşturur.
     *
     * @param string $method istenen HTTP metodu.
     * @param string $uri istenen URI.
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
            "Route not found: [{$method}] {$uri}",
            previous: $previous,
        );
    }

    /**
     * İstenen HTTP metodunu döndürür.
     *
     * @return string HTTP metodu.
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * İstenen URI'yi döndürür.
     *
     * @return string URI.
     */
    public function uri(): string
    {
        return $this->uri;
    }
}
