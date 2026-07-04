<?php
/*
 * Project:     beacon
 * File:        ApiAuthMiddleware.php
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Interfaces\MiddlewareInterface;
use App\Services\AuthService;

class ApiAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthService $auth,
    ) {}

    public function handle(Request $request, callable $next, mixed ...$params): Response
    {
        if (!$this->auth->check()) {
            return Response::json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}