<?php
/*
 * Project:     Beacon
 * File:        CsrfService.php
 * Date:        2026-06-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Services;

use Random\RandomException;

class CsrfService
{
    /**
     * @throws RandomException
     */
    public function token(): string
    {
        if (!isset($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public function validate(?string $token): bool {
        return isset($_SESSION['_csrf'])
            && $token !== null
            && hash_equals($_SESSION['_csrf'], $token);
    }

    /**
     * @throws RandomException
     */
    public function regenerate(): string
    {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));

        return $_SESSION['_csrf'];
    }

}