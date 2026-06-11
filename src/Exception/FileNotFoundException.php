<?php
// ============================================================================
// File:    FileNotFoundException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use Exception;
use Throwable;


class FileNotFoundException extends Exception
{
    public function __construct(
        string $path = '',
        int $code = 404,
        ?Throwable $previous = null,
    ) {
        $message = 'File not found';

        if ($path !== '') {
            $message .= ": {$path}";
        }

        parent::__construct($message, $code, $previous);
    }
}
