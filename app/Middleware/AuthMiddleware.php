<?php
/*
 * Project:     Beacon
 * File:        AuthMiddleware.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Services\AuthService;

final readonly class AuthMiddleware
{
    public function __construct(
        private AuthService $auth,
    ) { }

    public function handle(Request $request, callable $next): mixed
    {
        if (!$this->auth->check()) {
            $_SESSION['url.indented'] = $request->uri();

            return Response::redirect('/auth/login');
        }

        return $next($request);
    }
}