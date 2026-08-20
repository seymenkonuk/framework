<?php
// ============================================================================
// File:    Middleware.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http;


use Closure;

use Seymenkonuk\Framework\Http\Request\IRequest;
use Seymenkonuk\Framework\Http\Response\IResponse;


abstract class Middleware
{
    // --------------------------------------------------------------------------
    // HANDLE
    // --------------------------------------------------------------------------

    /**
     * Gelen isteği işler ve middleware zincirinin devamını çalıştırır.
     *
     * @param IRequest $request işlenecek HTTP isteği.
     * @param IResponse $response middleware zincirine aktarılacak response.
     * @param Closure(IRequest, IResponse): IResponse $next middleware zincirinin sonraki adımını çalıştırır.
     *
     * @return IResponse oluşturulan veya middleware zincirinden dönen response.
     */
    abstract public function handle(IRequest $request, IResponse $response, Closure $next): IResponse;
}
