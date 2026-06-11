<?php
// ============================================================================
// File:    Controller.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


abstract class Controller
{
    // --------------------------------------------------------------------------
    // DEPENDENCIES
    // --------------------------------------------------------------------------

    // public function __construct(
    //     protected Response $response,
    // ) {}

    // --------------------------------------------------------------------------
    // RESPONSE SHORTCUTS
    // --------------------------------------------------------------------------

    // /** @param array<string, mixed> $data */
    // public function view(string $viewName, array $data = []): Response
    // {
    //     return $this->response->view($viewName, $data);
    // }

    // /** @param array<string, mixed> $data */
    // public function component(string $componentName, array $data = []): Response
    // {
    //     return $this->response->component($componentName, $data);
    // }

    // /** @param array<string, mixed> $data */
    // public function json(array $data): Response
    // {
    //     return $this->response->json($data);
    // }

    // public function redirect(string $url): Response
    // {
    //     return $this->response->redirect($url);
    // }
}
