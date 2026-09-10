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


abstract class TestCase extends PHPUnitTestCase
{
    abstract protected function application(): Application;
}
