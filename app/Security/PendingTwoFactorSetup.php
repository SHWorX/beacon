<?php
/*
 * Project:     beacon
 * File:        PendingTwoFactorSetup.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Security;

use Carbon\CarbonImmutable;

final readonly class PendingTwoFactorSetup
{
    public function __construct(
        public string $id,
        public string $secret,
        public CarbonImmutable $createdAt,
    ) { }

    public function isExpired(int $minutes = 10): bool
    {
        return $this->createdAt
            ->addMinutes($minutes)
            ->isPast();
    }
}
