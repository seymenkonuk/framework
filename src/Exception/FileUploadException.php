<?php
// ============================================================================
// File:    FileUploadException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use Exception;
use Throwable;


class FileUploadException extends Exception
{
    public function __construct(
        string $message = 'File upload error',
        public ?int $errorCode = null,
        ?Throwable $previous = null,
    ) {
        if ($errorCode !== null) {
            parent::__construct(FileUploadException::messageFromCode($errorCode), previous: $previous);
        } else {
            parent::__construct($message, previous: $previous);
        }
    }

    public static function messageFromCode(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
            UPLOAD_ERR_PARTIAL => 'File partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temp folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
            UPLOAD_ERR_EXTENSION => 'Upload blocked by extension',
            default => 'Unknown upload error',
        };
    }
}
