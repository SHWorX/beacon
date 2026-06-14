<?php
/*
 * Project:     beacon
 * File:        mailer.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Support\Env;

return [
    'host' => Env::get('MAIL_HOST'),
    'port' => Env::int('MAIL_PORT', 587),
    'smtp_debug_level' => Env::bool('MAIL_DEBUG', 0),
    'username' => Env::get('MAIL_USERNAME', ''),
    'password' => Env::get('MAIL_PASSWORD', ''),
    'auth' => Env::bool('MAIL_AUTH', true),
    'encryption' => Env::get('MAIL_ENCRYPTION', ''),
    'from_address' => Env::get('MAIL_FROM_ADDRESS'),
    'from_name' => Env::get('MAIL_FROM_NAME'),
    'to_address' => Env::get('MAIL_TO_ADDRESS'),
    'to_name' => Env::get('MAIL_TO_NAME'),
];