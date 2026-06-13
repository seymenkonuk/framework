<?php
// ============================================================================
// File:    TemplateEngine.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use Throwable;

use League\Plates\Engine;

use voku\helper\HtmlMin;

use Seymenkonuk\Framework\Exception\TemplateException;


final class TemplateEngine
{
    // --------------------------------------------------------------------------
    //  PROPERTIES
    // --------------------------------------------------------------------------

    protected Engine $plates;
    protected HtmlMin $htmlMin;

    // --------------------------------------------------------------------------
    //  CONFIG
    // --------------------------------------------------------------------------

    protected string $dir = "Views";

    // --------------------------------------------------------------------------
    //  CONSTRUCTOR
    // --------------------------------------------------------------------------

    public function __construct(string $rootDir)
    {
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
    //  SET DIRECTORY
    // --------------------------------------------------------------------------

    public function setDirectory(string $dir): void
    {
        $this->dir = $dir;
    }

    // --------------------------------------------------------------------------
    //  RENDER
    // --------------------------------------------------------------------------

    /** @param array<string, mixed> $data */
    public function render(string $name, array $data = []): string
    {
        try {
            $html = $this->plates->render(rtrim($this->dir, "/") . "/Pages/" . ltrim($name, "/"), $data);
            $minifiedHtml = $this->htmlMin->minify($html);
            return $minifiedHtml;
        } catch (Throwable $e) {
            throw new TemplateException($e->getMessage(), previous: $e);
        }
    }

    /** @param array<string, mixed> $data */
    public function renderComponent(string $name, array $data = []): string
    {
        try {
            $html = $this->plates->render(rtrim($this->dir, "/") . "/Components/" . ltrim($name, "/"), $data);
            $minifiedHtml = $this->htmlMin->minify($html);
            return $minifiedHtml;
        } catch (Throwable $e) {
            throw new TemplateException($e->getMessage(), previous: $e);
        }
    }

    /** @param array<string, mixed> $data */
    public function renderError(int $code, array $data = []): string
    {
        try {
            $html = $this->plates->render("/Errors/" . ltrim((string)$code, "/"), $data);
            $minifiedHtml = $this->htmlMin->minify($html);
            return $minifiedHtml;
        } catch (Throwable $e) {
            throw new TemplateException($e->getMessage(), previous: $e);
        }
    }
}
