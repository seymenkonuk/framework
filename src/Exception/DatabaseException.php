<?php
// ============================================================================
// File:    DatabaseException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use Exception;
use Throwable;


class DatabaseException extends Exception
{
    public function __construct(
        string $message = "Database Exception",
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, previous: $previous);
    }
}
