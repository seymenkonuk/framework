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

    /** @param array<string, mixed> $data */
    public function render(string $name, array $data = []): string;
    /** @param array<string, mixed> $data */
    public function renderComponent(string $name, array $data = []): string;
    /** @param array<string, mixed> $data */
    public function renderError(int $code, array $data = []): string;

    // --------------------------------------------------------------------------
    // DRIVER
    // --------------------------------------------------------------------------

    public function driver(): string;
}
