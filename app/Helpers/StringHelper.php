<?php
/*
 * Project:     Beacon
 * File:        StringHelper.php
 * Date:        2026-06-11
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Helpers;

use App\Enums\AuthToken;
use App\Exceptions\InvalidEnumException;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Random\RandomException;

final readonly class StringHelper
{
    /**
     * Generates a random token
     *
     * @return string
     * @throws RandomException
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Returns a UUIDv7
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function uuid7(): string
    {
        return Uuid::uuid7()->toString();
    }

    /**
     * Generates a verification token incl. expiration time
     *
     * @param AuthToken $type
     *
     * @return array
     * @throws InvalidEnumException
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function getVerificationToken(AuthToken $type): array
    {
        $token = self::generateToken();
        if ($type === AuthToken::EMAIL) {
            $tokenExpiration = config('app.email_verification_token_expiry');
        } elseif ($type === AuthToken::PASSWORD) {
            $tokenExpiration = config('app.password_reset_token_expiry');
        } else {
            throw new InvalidEnumException('Unsupported token type');
        }

        $now = Carbon::now();

        if (str_ends_with($tokenExpiration, 'h')) {
            $expirationTime = (int)substr($tokenExpiration, 0, strlen($tokenExpiration) - 1);
            $expiresAt = $now->addHours($expirationTime);
        } elseif (str_ends_with($tokenExpiration, 'd')) {
            $expirationTime = (int)substr($tokenExpiration, 0, strlen($tokenExpiration) - 1);
            $expiresAt = $now->addDays($expirationTime);
        } else {
            $expiresAt = $now->addHours(48);
        }

        return [
            'token' => $token,
            'expires_at' => $expiresAt,
        ];
    }
}