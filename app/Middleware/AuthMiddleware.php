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
use App\Interfaces\MiddlewareInterface;
use App\Services\AuthService;

final readonly class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthService $auth,
    ) { }

    public function handle(Request $request, callable $next, mixed ...$parameters): Response
    {
//        $uri = $this->normalizeRoute($request->uri());
//
//        $publicRoutes = [
//            '/',
//            'auth/forgotten',
//            'auth/login',
//            'auth/register',
//            'auth/resend_verification',
//            'auth/reset/*',
//            'auth/verify/*',
//        ];
//
//        if (array_any($publicRoutes, fn (string $pattern) => fnmatch($this->normalizeRoute($pattern), $uri))) {
//            return $next($request);
//        }

        if (!$this->auth->check()) {
            $_SESSION['url.indented'] = $request->uri();

            return Response::redirect('/auth/login');
        }

        return $next($request);
    }

//    private function normalizeRoute(string $path): string {
//        if ($path !== '/') {
//            return rtrim($path, '/');
//        }
//        return $path;
//    }
}
