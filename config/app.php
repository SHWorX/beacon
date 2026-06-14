<?php
/*
 * Project:     Beacon
 * File:        app.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Support\Env;
use Monolog\Logger;

return [
    'name' => Env::get('APP_NAME', 'Beacon'),
    'slogan' => Env::get('APP_SLOGAN', 'The Foundation for Modern PHP Applications'),
    'version' => Env::get('APP_VERSION'),
    'copyright' => Env::get('APP_COPYRIGHT', 'Copyright © 2026 <a href="https://shworx.com" target="_blank">SHworX</a>. All rights reserved.'),
    'env' => Env::get('APP_ENV', 'production'),
    'debug' => Env::bool('APP_DEBUG'),
    'url' => Env::get('APP_URL', 'http://localhost'),
    'cookieDomain' => Env::get('APP_COOKIE_DOMAIN'),
    'cookieSecure' => Env::bool('APP_COOKIE_SECURE'),
    'cookieSameSite' => Env::get('APP_COOKIE_SAMESITE', 'lax'),
    'cookieLifetimeRememberMe' => Env::int('APP_COOKIE_LIFETIME_REMEMBER_ME', 30),
    'timezone' => Env::get('APP_TIMEZONE', 'UTC'),
    'logLevel' => Env::get('APP_LOG_LEVEL', 'error'),
    'secret' => Env::get('APP_SECRET', ''),
    'email_verification_token_expiry' => Env::get('APP_EMAIL_VERIFICATION_TOKEN_EXPIRY', '48h'),
    'password_reset_token_expiry' => Env::get('APP_PASSWORD_RESET_TOKEN_EXPIRY', '2h'),
];