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
use App\Http\Response;
use App\Interfaces\MiddlewareInterface;
use App\Services\AuthService;
use App\Support\Cookie;
use Random\RandomException;

final readonly class RememberMeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthService $auth,
    ) { }

    /**
     * @throws RandomException
     */
    public function handle(Request $request, callable $next, mixed ...$parameters): Response
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