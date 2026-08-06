<?php
// ============================================================================
// File:    ITemplateEngine.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\TemplateEngine;


interface ITemplateEngine
{
    // --------------------------------------------------------------------------
    // RENDER
    // --------------------------------------------------------------------------

    /**
     * Belirtilen template'i verilen verilerle render eder.
     * 
     * $data dizisinin anahtarları template içerisinde kullanılabilecek
     * değişken isimlerini, değerleri ise bu değişkenlere aktarılacak verileri
     * temsil eder.
     * 
     * @param string $name render edilecek template'in adı.
     * @param array<string, mixed> $data template'e aktarılacak veriler.
     * 
     * @return string render edilen template içeriği.
     */
    public function render(string $name, array $data = []): string;

    /**
     * Belirtilen component template'ini verilen verilerle render eder.
     * 
     * Bu metot, tam sayfa template'lerinden bağımsız olarak tekrar
     * kullanılabilen küçük arayüz parçalarını render etmek için kullanılır.
     * 
     * $data dizisinin anahtarları component içerisinde kullanılabilecek
     * değişken isimlerini, değerleri ise bu değişkenlere aktarılacak verileri
     * temsil eder.
     * 
     * @param string $name render edilecek component'in adı.
     * @param array<string, mixed> $data component'e aktarılacak veriler.
     * 
     * @return string render edilen component içeriği.
     */
    public function renderComponent(string $name, array $data = []): string;

    /**
     * Belirtilen durum koduna ait hata template'ini verilen verilerle render
     * eder.
     * 
     * $code değeri, render edilecek hata template'inin belirlenmesinde
     * kullanılır.
     * 
     * $data dizisinin anahtarları hata template'i içerisinde kullanılabilecek
     * değişken isimlerini, değerleri ise bu değişkenlere aktarılacak verileri
     * temsil eder.
     * 
     * @param int $code render edilecek hata template'inin durum kodu.
     * @param array<string, mixed> $data hata template'ine aktarılacak veriler.
     * 
     * @return string render edilen hata template'i içeriği.
     */
    public function renderError(int $code, array $data = []): string;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    /**
     * Kullanılan template engine sürücüsünün adını döndürür.
     * 
     * Örneğin: plates, twig, blade...
     * 
     * @return string template engine sürücüsünün adı.
     */
    public function driver(): string;
}
