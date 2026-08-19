<?php
// ============================================================================
// File:    NullTemplateEngine.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\TemplateEngine;


final class NullTemplateEngine implements ITemplateEngine
{
    // --------------------------------------------------------------------------
    //  RENDER
    // --------------------------------------------------------------------------

    public function render(string $name, array $data = []): string
    {
        return "";
    }

    public function renderComponent(string $name, array $data = []): string
    {
        return "";
    }

    public function renderError(int $code, array $data = []): string
    {
        return "";
    }

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string
    {
        return "null";
    }
}
