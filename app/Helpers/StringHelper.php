<?php
/*
 * Project:     Beacon
 * File:        StringHelper.php
 * Date:        2026-06-11
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Helpers;

use Ramsey\Uuid\Uuid;
use Random\RandomException;

final readonly class StringHelper
{
    /**
     * Generates a random token
     *
     * @return string
     * @throws RandomException
     * @author Steffen Haase <shworx.development@gmail.com
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
}