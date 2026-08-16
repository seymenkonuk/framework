<?php
// ============================================================================
// File:    PhpUploadedFile.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\UploadedFile;


use finfo;

use Seymenkonuk\Framework\Exception\FileUploadException;


final class PhpUploadedFile implements IUploadedFile
{
    // --------------------------------------------------------------------------
    //  PROPERTIES
    // --------------------------------------------------------------------------

    /**
     * Hesaplanan MIME türünü tekrar hesaplanmaması için saklar.
     *
     * @var ?string
     */
    private ?string $cachedMime = null;

    // --------------------------------------------------------------------------
    //  CONSTRUCTOR
    // --------------------------------------------------------------------------

    /**
     * Belirtilen PHP dosya bilgileriyle dosya nesnesini oluşturur.
     *
     * @param array{
     *     name: string,
     *     type: string,
     *     tmp_name: string,
     *     error: int,
     *     size: int
     * } $file PHP tarafından sağlanan dosya bilgileri.
     *
     * @return void
     */
    public function __construct(
        protected array $file,
    ) {}

    // --------------------------------------------------------------------------
    //  GETTERS
    // --------------------------------------------------------------------------

    public function valid(): bool
    {
        return $this->file["error"] === UPLOAD_ERR_OK
            && is_uploaded_file($this->file["tmp_name"]);
    }

    public function name(): string
    {
        return basename($this->file["name"]);
    }

    public function temp(): string
    {
        return $this->file["tmp_name"];
    }

    public function size(): int
    {
        return $this->file["size"];
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->file["name"], PATHINFO_EXTENSION));
    }

    public function mime(): string
    {
        if ($this->cachedMime !== null) {
            return $this->cachedMime;
        }

        if (!$this->valid()) {
            throw new FileUploadException("Invalid upload", $this->file["error"]);
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $this->cachedMime = $finfo->file($this->file["tmp_name"])
            ?: "application/octet-stream";

        return $this->cachedMime;
    }

    // --------------------------------------------------------------------------
    //  MOVE
    // --------------------------------------------------------------------------

    public function move(string $destination, ?string $newName = null): string
    {
        if (!$this->valid()) {
            throw new FileUploadException("Invalid upload", $this->file["error"]);
        }

        if (!is_dir($destination) && !mkdir($destination, 0777, true)) {
            throw new FileUploadException("Failed to create directory");
        }

        $newName ??= $this->name();
        $newName = basename($newName);

        $target = rtrim($destination, "/") . "/" . ltrim($newName, "/");

        if (!move_uploaded_file($this->temp(), $target)) {
            throw new FileUploadException("Move failed");
        }

        return $target;
    }
}
