<?php
/*
 * Project:     beacon
 * File:        RateLimitResult.php
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Security;

readonly class RateLimitResult
{
    public function __construct(
        public bool $allowed,
        public int $remaining,
        public int $retry_after,
        public int $limit
    ) { }
}
