<?php
// ============================================================================
// File:    IRequestSchema.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\RequestSchema;


interface IRequestSchema
{
    // --------------------------------------------------------------------------
    //  VALIDATE
    // --------------------------------------------------------------------------

    /**
     * Belirtilen veriyi şema kurallarına göre doğrular.
     * 
     * $exists değeri, doğrulanacak verinin kaynak veri içerisinde gerçekten
     * mevcut olup olmadığını belirtir.
     * 
     * Bir değer mevcut olmadığı için null gönderildiğinde $exists false
     * verilmelidir. Değer gerçekten mevcut ve değeri null ise $exists true
     * olarak bırakılmalıdır.
     * 
     * Örneğin:
     * 
     * validate($data["name"] ?? null, array_key_exists("name", $data));
     * 
     * Bu sayede mevcut olmayan bir değer ile gerçekten null olan bir değer
     * birbirinden ayırt edilebilir.
     * 
     * @param mixed $data doğrulanacak veri.
     * @param bool $exists verinin gerçekten mevcut olup olmadığı.
     * 
     * @return ValidationResult doğrulama sonucu.
     */
    public function validate(mixed $data, bool $exists = true): ValidationResult;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    /**
     * Kullanılan doğrulama şeması sürücüsünün adını döndürür.
     * 
     * Örneğin: seymenkonuk/validator...
     * 
     * @return string doğrulama şeması sürücüsünün adı.
     */
    public function driver(): string;
}
