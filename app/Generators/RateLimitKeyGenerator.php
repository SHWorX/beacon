<?php
/*
 * Project:     beacon
 * File:        RateLimitKeyGenerator.php
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Generators;

use App\Http\Request;
use App\Security\RateLimitResult;
use App\Services\AuthService;

readonly class RateLimitKeyGenerator
{
    /**
     * @param string $method Request method
     * @param string $uri Request URI
     * @param string|null $userId User ID
     * @param string|null $ip IP
     * @param string|null $routeName Route name
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function generate(
        string $method,
        string $uri,
        ?string $userId = null,
        ?string $ip = null,
        ?string $routeName = null
    ): string {
        // Prefer user-based limiting if user is authenticated
        if ($userId !== null) {
            return hash('sha256', sprintf(
                'rl:user:%s:%s:%s',
                $userId,
                $method,
                $routeName ?? $uri,
            ));
        }

        // Fallback to ip-based limiting
        return hash('sha256', sprintf(
            'rl:ip:%s:%s:%s',
            $ip,
            $method,
            $routeName ?? $uri,
        ));
    }
}