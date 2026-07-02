<?php
/*
 * Project:     beacon
 * File:        twofactor.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

return [
    /* Name shown inside authenticator apps. */
    'issuer' => env('APP_NAME', 'Beacon'),

    /* Digits of generated TOTP. */
    'digits' => 6,

    /* Time step in seconds. */
    'period' => 30,

    /*
     * Allowed clock drift.
     *
     * 1 = previous/current/next window
     */
    'window' => 1,

    /* Number of recovery codes. */
    'recovery_codes' => 8,
];