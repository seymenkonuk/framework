<?php
// ============================================================================
// File:    AuthorizationException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Exception;


use Exception;
use Throwable;


class AuthorizationException extends Exception
{
    public function __construct(
        protected string $title,
        protected string $description,
        ?Throwable $previous = null,
    ) {
        parent::__construct("Yetkilendirme Hatası", previous: $previous);
    }

    /**  
     * @return array{
     *      title: string,
     *      description: string
     * }
     */
    public function errors(): array
    {
        return [
            "title" => $this->title,
            "description" => $this->description,
        ];
    }
}
