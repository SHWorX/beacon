<?php
/*
 * Project:     beacon
 * File:        RateLimiter.php
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Security;

use App\Models\RateLimit;
use Carbon\Carbon;
use Illuminate\Database\Connection;
use Random\RandomException;
use Throwable;

readonly class RateLimiter
{
    public function __construct(
        private Connection $db,
    ) {}

    /**
     * @throws RandomException
     * @throws Throwable
     */
    public function hit(
        string $key,
        int $maxAttempts,
        int $decayMinutes,
    ): RateLimitResult {
        // Probabilistic cleanup
        if (random_int(1, 100) === 1) {
            RateLimit::query()->where('expires_at', '<=', Carbon::now())->delete();
        }

        $windowSeconds = $decayMinutes * 60;

        return $this->db->transaction(function () use ($key, $maxAttempts, $windowSeconds) {
            $now = Carbon::now();

            /** @var RateLimit|null $rateLimit */
            $rateLimit = RateLimit::query()
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            // First request
            if ($rateLimit === null) {
                RateLimit::create([
                    'key' => $key,
                    'hits' => 1,
                    'window_start' => $now,
                    'expires_at' => $now->copy()->addSeconds($windowSeconds),
                ]);

                return new RateLimitResult(
                    allowed: true,
                    remaining: $maxAttempts -1,
                    retry_after: $windowSeconds,
                    limit: $maxAttempts,
                );
            }

            // Window expired > reset
            if ($rateLimit->expires_at->lessThanOrEqualTo($now)) {
                $rateLimit->hits = 1;
                $rateLimit->window_start = $now;
                $rateLimit->expires_at = $now->copy()->addSeconds($windowSeconds);
            } else {
                // Same window > increment
                $rateLimit->hits++;
            }

            $rateLimit->save();

            $allowed = $rateLimit->hits <= $maxAttempts;

            return new RateLimitResult(
                allowed: $allowed,
                remaining: max(0, $maxAttempts - $rateLimit->hits),
                retry_after: max(0, $now->diffInSeconds($rateLimit->expires_at)),
                limit: $maxAttempts,
            );
        });
    }

    public function clear(string $key): void
    {
        RateLimit::query()->where('key', $key)->delete();
    }

    public function availableIn(string $key): int
    {
        $expiresAt = RateLimit::query()
            ->where('key', $key)
            ->value('expires_at');

        if ($expiresAt === null) {
            return 0;
        }

        return max(0, Carbon::now()->diffInSeconds($expiresAt) * -1);
    }
}
