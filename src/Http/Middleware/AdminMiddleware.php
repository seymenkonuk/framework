<?php
// ============================================================================
// File:    AdminMiddleware.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Http\Middleware;


use Closure;

use Seymenkonuk\Framework\Auth\IAdminAuthService;

use Seymenkonuk\Framework\Exception\AdminAuthorizationException;
use Seymenkonuk\Framework\Exception\AuthenticationRequiredException;

use Seymenkonuk\Framework\Http\Middleware;
use Seymenkonuk\Framework\Http\Request\IRequest;
use Seymenkonuk\Framework\Http\Response\IResponse;


final class AdminMiddleware extends Middleware
{
    public function __construct(
        protected IAdminAuthService $authService,
    ) {}

    public function handle(IRequest $request, IResponse $response, Closure $next): IResponse
    {
        // Giriş Yapmamışsa Hata Fırlat
        if (!$this->authService->authenticated()) {
            throw new AuthenticationRequiredException();
        }

        // Admin Değilse Hata Fırlat
        if (!$this->authService->isAdmin()) {
            throw new AdminAuthorizationException();
        }

        // Normal İşleme Devam Et
        return $next($request, $response);
    }
}
