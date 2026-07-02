<?php
/*
 * Project:     Beacon
 * File:        GuestMiddleware.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Interfaces\MiddlewareInterface;
use App\Services\AuthService;

final readonly class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected AuthService $auth,
    ) { }

    public function handle(Request $request, callable $next, mixed ...$parameters): Response
    {
        if ($this->auth->check()) {
            return Response::redirect('/');
        }

        return $next($request);
    }
}