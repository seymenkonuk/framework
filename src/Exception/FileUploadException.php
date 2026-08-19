<?php
// ============================================================================
// File:    FileUploadException.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Exception;


use RuntimeException;
use Throwable;


/**
 * Dosya yükleme işlemi sırasında oluşan hatayı temsil eder.
 */
class FileUploadException extends RuntimeException
{
    /**
     * Yeni bir file upload exception oluşturur.
     *
     * Upload hata kodu belirtilmişse hata mesajı bu koda göre oluşturulur.
     *
     * @param string $message hata mesajı.
     * @param ?int $uploadCode PHP upload hata kodu veya null.
     * @param ?Throwable $previous önceki exception veya null.
     *
     * @return void
     */
    public function __construct(
        string $message = "File upload failed.",
        protected readonly ?int $uploadCode = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            $uploadCode !== null
                ? self::messageFromCode($uploadCode)
                : $message,
            previous: $previous,
        );
    }

    /**
     * PHP upload hata kodundan açıklayıcı bir mesaj oluşturur.
     *
     * @param int $code PHP upload hata kodu.
     *
     * @return string hata mesajı.
     */
    private static function messageFromCode(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => "File exceeds the upload_max_filesize limit.",
            UPLOAD_ERR_FORM_SIZE => "File exceeds the form upload limit.",
            UPLOAD_ERR_PARTIAL => "File was only partially uploaded.",
            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "The temporary upload directory is missing.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write the uploaded file.",
            UPLOAD_ERR_EXTENSION => "File upload was stopped by an extension.",
            default => "Unknown file upload error.",
        };
    }

    /**
     * PHP upload hata kodunu döndürür.
     *
     * @return ?int upload hata kodu veya null.
     */
    public function uploadCode(): ?int
    {
        return $this->uploadCode;
    }
}
