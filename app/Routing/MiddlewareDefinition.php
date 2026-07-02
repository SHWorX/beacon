<?php
/*
 * Project:     beacon
 * File:        MiddlewareDefinition.php
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Routing;

final readonly class MiddlewareDefinition
{
    /**
     * @param class-string $class
     * @param array<string> $parameters
     */
    public function __construct(
        public string $class,
        public array $parameters = [],
    ) {}
}