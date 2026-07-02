<?php
/*
 * Project:     beacon
 * File:        RouteExecutionContext.php
 * Date:        2026-06-29
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Routing;

use App\Http\Request;

readonly class RouteExecutionContext
{
    /**
     * @param array<string, mixed> $parameters
     * @param array<int, MiddlewareDefinition> $middleware
     */
    public function __construct(
        public Request $request,
        public string $routeName,
        public mixed $handler,
        public array $parameters,
        public array $middleware,
    ) {}
}