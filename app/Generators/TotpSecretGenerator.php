<?php
/*
 * Project:     beacon
 * File:        TotpSecretGenerator.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Generators;

use Random\RandomException;

final readonly class TotpSecretGenerator
{
    private const string ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Returns a secret
     *
     * @param int $length
     *
     * @return string
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function generate(int $length = 32): string
    {
        $secret = '';
        $max = strlen(self::ALPHABET) - 1;

        while (strlen($secret) < $length) {
            $secret .= self::ALPHABET[random_int(0, $max)];
        }

        return $secret;
    }
}
