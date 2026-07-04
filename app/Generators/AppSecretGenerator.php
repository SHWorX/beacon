<?php
/*
 * Project:     beacon
 * File:        AppSecretGenerator.php
 * Date:        2026-06-20
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Generators;

use Random\RandomException;

final readonly class AppSecretGenerator
{
    /**
     * Generate a cryptographically secure application secret
     *
     * @param int $length [optional] Secret length in bytes (default: 32)
     *
     * @return string
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function generate(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}