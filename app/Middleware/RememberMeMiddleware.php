<?php
/*
 * Project:     Beacon
 * File:        RememberMeMiddleware.php
 * Date:        2026-06-11
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Middleware;

use App\Http\Request;
use App\Services\AuthService;
use App\Support\Cookie;

final readonly class RememberMeMiddleware
{
    public function __construct(
        private readonly AuthService $auth,
    ) { }

    public function handle(Request $request, callable $next): mixed
    {
        if ($this->auth->check()) {
            return $next($request);
        }

        $token = Cookie::get('remember_token');

        if ($token !== null) {
            $this->auth->loginFromRememberToken($token);
        }

        return $next($request);
    }
}