<?php
/*
 * Project:     Beacon
 * File:        CsrfMiddleware.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Interfaces\MiddlewareInterface;
use App\Services\CsrfService;
use Psr\Log\LoggerInterface;

readonly class CsrfMiddleware implements MiddlewareInterface
{
    public function __construct(
        private CsrfService $csrf,
        private LoggerInterface $logger,
    ) { }

    public function handle(Request $request, callable $next): Response
    {
        if ($request->method() === 'POST') {
            $this->logger->debug("CSRF token validation", [
                'token' => $request->post('_csrf'),
                'session_token' => $_SESSION['_csrf'],
                'hash_equals' => hash_equals($_SESSION['_csrf'], $request->post('_csrf')),
            ]);

            $token = $request->post('_csrf');

            if (!$this->csrf->validate($token)) {
                return Response::html('<h1>HTTP 419 - CSRF Token Mismatch</h1>', 419);
            }
        }

        return $next($request);
    }
}