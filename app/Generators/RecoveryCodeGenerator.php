<?php
/*
 * Project:     beacon
 * File:        RecoveryCodeGenerator.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Generators;

use Random\RandomException;

/**
 * TOTP recovery codes generator
 */
final readonly class RecoveryCodeGenerator
{
    /**
     * Returns a set of recovery codes
     *
     * @param int $count [optional] Amount of codes to generate (default: 8)
     *
     * @return array
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function generate(int $count = 8): array
    {
        $codes = [];

        while (count($codes) < $count) {
            $codes[] = sprintf(
                '%s-%s',
                bin2hex(random_bytes(4)),
                bin2hex(random_bytes(4)),
            );
        }

        return $codes;
    }
}
