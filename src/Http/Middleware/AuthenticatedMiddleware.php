<?php
// ============================================================================
// File:    AuthenticatedMiddleware.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Middleware;


use Closure;

use Seymenkonuk\Framework\Auth\IAuthService;

use Seymenkonuk\Framework\Exception\AuthenticationRequiredException;

use Seymenkonuk\Framework\Http\Middleware;
use Seymenkonuk\Framework\Http\Request\IRequest;
use Seymenkonuk\Framework\Http\Response\IResponse;


final class AuthenticatedMiddleware extends Middleware
{
    public function __construct(
        protected IAuthService $authService,
    ) {}

    public function handle(IRequest $request, IResponse $response, Closure $next): IResponse
    {
        // Giriş Yapmamışsa Hata Fırlat
        if (!$this->authService->authenticated()) {
            throw new AuthenticationRequiredException();
        }

        // Normal İşleme Devam Et
        return $next($request, $response);
    }
}
