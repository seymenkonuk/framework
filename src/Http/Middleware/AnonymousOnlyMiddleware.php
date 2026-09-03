<?php
// ============================================================================
// File:    AnonymousOnlyMiddleware .php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Middleware;


use Closure;

use Seymenkonuk\Framework\Auth\IAuthService;

use Seymenkonuk\Framework\Exception\AlreadyAuthenticatedException;

use Seymenkonuk\Framework\Http\Middleware;
use Seymenkonuk\Framework\Http\Request\IRequest;
use Seymenkonuk\Framework\Http\Response\IResponse;


final class AnonymousOnlyMiddleware extends Middleware
{
    public function __construct(
        protected IAuthService $authService,
    ) {}

    public function handle(IRequest $request, IResponse $response, Closure $next): IResponse
    {
        // Giriş Yapmışsa Hata Fırlat
        if ($this->authService->authenticated()) {
            throw new AlreadyAuthenticatedException();
        }

        // Normal İşleme Devam Et
        return $next($request, $response);
    }
}
