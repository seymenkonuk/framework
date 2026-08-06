<?php
// ============================================================================
// File:    ValidationResult.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\RequestSchema;


final class ValidationResult
{
    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    /**
     * Yeni bir doğrulama sonucu oluşturur.
     * 
     * $isValid değeri doğrulama işleminin başarılı olup olmadığını belirtir.
     * $errors değeri doğrulama sırasında oluşan hata veya hataları içerir.
     * $validated değeri ise doğrulama işleminden geçen veriyi temsil eder.
     * 
     * Başarılı bir doğrulamada $errors değerinin boş olması beklenir.
     * Başarısız bir doğrulamada $validated değeri beklenmedik olabilir.
     * 
     * @param bool $isValid doğrulama işleminin başarılı olup olmadığı.
     * @param array<string, mixed>|string $errors doğrulama sırasında oluşan hatalar.
     * @param mixed $validated doğrulama işleminden geçen veri.
     */
    public function __construct(
        protected bool $isValid,
        protected array|string $errors,
        protected mixed $validated,
    ) {}

    // --------------------------------------------------------------------------
    // GETTERS
    // --------------------------------------------------------------------------

    /**
     * Doğrulama işleminin başarılı olup olmadığını döndürür.
     * 
     * @return bool doğrulama başarılıysa true, aksi halde false.
     */
    public function passed(): bool
    {
        return $this->isValid;
    }

    /**
     * Doğrulama işleminin başarısız olup olmadığını döndürür.
     * 
     * @return bool doğrulama başarısızsa true, aksi halde false.
     */
    public function failed(): bool
    {
        return !$this->isValid;
    }

    /**
     * Doğrulama sırasında oluşan hata veya hataları döndürür.
     * 
     * @return array<string, mixed>|string doğrulama sırasında oluşan hatalar.
     */
    public function errors(): array|string
    {
        return $this->errors;
    }

    /**
     * Doğrulama işleminden geçen veriyi döndürür.
     * 
     * Döndürülen değer doğrulama sırasında dönüştürülmüş, temizlenmiş veya
     * varsayılan bir değerle değiştirilmiş olabilir.
     * 
     * @return mixed doğrulama işleminden geçen veri.
     */
    public function validated(): mixed
    {
        return $this->validated;
    }
}
