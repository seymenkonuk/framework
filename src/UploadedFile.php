<?php
// ============================================================================
// File:    UploadedFile.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use finfo;

use Seymenkonuk\Framework\Exception\FileUploadException;

use Seymenkonuk\Validator\Contract\IFile;


final class UploadedFile implements IFile
{
    // --------------------------------------------------------------------------
    //  CACHES
    // --------------------------------------------------------------------------

    private ?string $cachedMime = null;

    // --------------------------------------------------------------------------
    //  CONSTRUCTOR
    // --------------------------------------------------------------------------

    /**
     * @param array{
     *     name: string,
     *     type: string,
     *     full_path?: string,
     *     tmp_name: string,
     *     error: int,
     *     size: int
     * } $file
     */
    public function __construct(private array $file) {}

    // --------------------------------------------------------------------------
    //  IS VALID
    // --------------------------------------------------------------------------

    public function isValid(): bool
    {
        return $this->file["error"] === UPLOAD_ERR_OK
            && is_uploaded_file($this->file["tmp_name"]);
    }

    // --------------------------------------------------------------------------
    //  PROPERTIES
    // --------------------------------------------------------------------------

    public function getName(): string
    {
        return basename($this->file["name"]);
    }

    public function getSize(): int
    {
        return $this->file["size"];
    }

    public function getExtension(): string
    {
        return strtolower(pathinfo($this->file["name"], PATHINFO_EXTENSION));
    }

    public function getMimeType(): string
    {
        if ($this->cachedMime !== null) {
            return $this->cachedMime;
        }

        if (!$this->isValid()) {
            throw new FileUploadException("Invalid upload", $this->file["error"]);
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $this->cachedMime = $finfo->file($this->file["tmp_name"])
            ?: "application/octet-stream";

        return $this->cachedMime;
    }

    public function getTmpPath(): string
    {
        return $this->file["tmp_name"];
    }

    // --------------------------------------------------------------------------
    //  MOVE
    // --------------------------------------------------------------------------

    public function move(string $destination, ?string $newName = null): string
    {
        if (!$this->isValid()) {
            throw new FileUploadException("Invalid upload", $this->file["error"]);
        }

        if (!is_dir($destination) && !mkdir($destination, 0777, true)) {
            throw new FileUploadException("Failed to create directory");
        }

        $newName ??= $this->getName();
        $newName = basename($newName);

        $target = rtrim($destination, "/") . "/" . ltrim($newName, "/");

        if (!move_uploaded_file($this->getTmpPath(), $target)) {
            throw new FileUploadException("Move failed");
        }

        return $target;
    }
}
