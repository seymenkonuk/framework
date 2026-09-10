<?php
// ============================================================================
// File:    TestCase.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Testing;


use PHPUnit\Framework\TestCase as PHPUnitTestCase;

use Seymenkonuk\Framework\Application;
use Seymenkonuk\Framework\Http\Response\ResponseState;


abstract class TestCase extends PHPUnitTestCase
{
    // --------------------------------------------------------------------------
    // APPLICATION
    // --------------------------------------------------------------------------

    abstract protected function application(): Application;

    // --------------------------------------------------------------------------
    // REQUEST OPTIONS
    // --------------------------------------------------------------------------

    abstract protected function withCookie(string $name, string $value): self;
    abstract protected function withCookies(array $cookies): self;

    abstract protected function withHeader(string $name, string $value): self;
    abstract protected function withHeaders(array $headers): self;

    abstract protected function withServer(string $name, string $value): self;
    abstract protected function withServers(array $servers): self;

    // --------------------------------------------------------------------------
    // FORM REQUEST
    // --------------------------------------------------------------------------

    abstract protected function get(string $uri): ResponseState;
    abstract protected function query(string $uri, array $data): ResponseState;
    abstract protected function post(string $uri, array $data): ResponseState;
    abstract protected function put(string $uri, array $data): ResponseState;
    abstract protected function patch(string $uri, array $data): ResponseState;
    abstract protected function delete(string $uri, array $data): ResponseState;

    // --------------------------------------------------------------------------
    // JSON REQUEST
    // --------------------------------------------------------------------------

    abstract protected function getJson(string $uri): ResponseState;
    abstract protected function queryJson(string $uri, array $data): ResponseState;
    abstract protected function postJson(string $uri, array $data): ResponseState;
    abstract protected function putJson(string $uri, array $data): ResponseState;
    abstract protected function patchJson(string $uri, array $data): ResponseState;
    abstract protected function deleteJson(string $uri, array $data): ResponseState;
}
