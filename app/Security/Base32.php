<?php
/*
 * Project:     beacon
 * File:        Base32.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Security;

use InvalidArgumentException;

/**
 * Base32 decoder for TOTP *
 * RFC 6238 uses Base32 secrets.
 */
final class Base32
{
    private const string ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * @param string $input
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public static function decode(string $input): string
    {
        $input = strtoupper($input);

        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        foreach (str_split($input) as $char) {
            $value = strpos(self::ALPHABET, $char);
            if ($value === false) {
                throw new InvalidArgumentException(
                    "Invalid Base32 character [$char]."
                );
            }

            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xff);
            }
        }

        return $output;
    }
}
