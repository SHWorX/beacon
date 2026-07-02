<?php
/*
 * Project:     beacon
 * File:        ApiMiddleware.php
 * Date:        2026-06-29
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Interfaces\MiddlewareInterface;

class ApiMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next, mixed ...$parameters): Response
    {
        return $next($request);
    }
}