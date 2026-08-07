<?php
// ============================================================================
// File:    PlatesTemplateEngine.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\TemplateEngine;


use Throwable;

use League\Plates\Engine;

use voku\helper\HtmlMin;

use Seymenkonuk\Framework\Exception\TemplateException;


final class PlatesTemplateEngine implements ITemplateEngine
{
    // --------------------------------------------------------------------------
    //  PROPERTIES
    // --------------------------------------------------------------------------

    protected Engine $plates;
    protected HtmlMin $htmlMin;

    // --------------------------------------------------------------------------
    //  CONSTRUCTOR
    // --------------------------------------------------------------------------

    public function __construct(
        protected string $rootDir,
    ) {
        // Template Engine Config
        $this->plates = new Engine($rootDir);
        // Minifier Config
        $this->htmlMin = new HTMLMin();
        $this->htmlMin->doRemoveOmittedHtmlTags(false);
        $this->htmlMin->doOptimizeAttributes(true);
        $this->htmlMin->doRemoveComments(true);
        $this->htmlMin->doRemoveSpacesBetweenTags(true);
        $this->htmlMin->doSumUpWhitespace(true);
        $this->htmlMin->doRemoveWhitespaceAroundTags(true);
        $this->htmlMin->doRemoveOmittedQuotes(true);
        $this->htmlMin->doRemoveDeprecatedAnchorName(true);
        $this->htmlMin->doKeepHttpAndHttpsPrefixOnExternalAttributes(true);
    }

    // --------------------------------------------------------------------------
    //  RENDER
    // --------------------------------------------------------------------------

    /** @throws TemplateException */
    public function render(string $name, array $data = []): string
    {
        return $this->renderTemplate("/Pages/" . ltrim($name, "/"), $data);
    }

    /** @throws TemplateException */
    public function renderComponent(string $name, array $data = []): string
    {
        return $this->renderTemplate("/Components/" . ltrim($name, "/"), $data);
    }

    /** @throws TemplateException */
    public function renderError(int $code, array $data = []): string
    {
        return $this->renderTemplate("/Errors/" . ltrim((string)$code, "/"), $data);
    }

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string
    {
        return "plates";
    }

    // --------------------------------------------------------------------------
    // INTERNAL
    // --------------------------------------------------------------------------

    /**
     * Path'i verilen template'i verilen verilerle render eder.
     * 
     * $data dizisinin anahtarları template içerisinde kullanılabilecek
     * değişken isimlerini, değerleri ise bu değişkenlere aktarılacak verileri
     * temsil eder.
     * 
     * @param string $path render edilecek template'in path'i.
     * @param array<string, mixed> $data template'e aktarılacak veriler.
     * 
     * @throws TemplateException
     * 
     * @return string render edilen template içeriği.
     */
    private function renderTemplate(string $path, array $data = []): string
    {
        try {
            $html = $this->plates->render($path, $data);
            return $this->htmlMin->minify($html);
        } catch (Throwable $e) {
            throw new TemplateException($e->getMessage(), previous: $e);
        }
    }
}
