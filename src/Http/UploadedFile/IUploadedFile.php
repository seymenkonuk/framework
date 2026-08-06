<?php
// ============================================================================
// File:    IUploadedFile.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\UploadedFile;


use Seymenkonuk\Validator\Contract\IFile;


interface IUploadedFile extends IFile
{
    /**
     * Yüklenen dosyayı belirtilen hedef dizine taşır.
     * 
     * $newName değeri verilirse dosya belirtilen isimle kaydedilir.
     * null verilirse dosyanın mevcut adı kullanılır.
     * 
     * Taşıma işlemi başarıyla tamamlandığında dosyanın yeni konumu döndürülür.
     * 
     * @param string $destination dosyanın taşınacağı hedef dizin.
     * @param ?string $newName dosyanın yeni adı.
     * 
     * @return string taşınan dosyanın yeni konumu.
     */
    public function move(string $destination, ?string $newName = null): string;
}
