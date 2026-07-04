<?php
/*
 * Project:     beacon
 * File:        middleware.php
 * Date:        2026-06-29
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Middleware\ApiAuthMiddleware;
use App\Middleware\ApiMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\RememberMeMiddleware;
use App\Middleware\ThrottleMiddleware;

return [
    'auth' => AuthMiddleware::class,
    'api_auth' => ApiAuthMiddleware::class,

    'guest' => GuestMiddleware::class,
    'csrf' => CsrfMiddleware::class,
    'remember'=> RememberMeMiddleware::class,
    'api' => ApiMiddleware::class,
    'throttle' => ThrottleMiddleware::class,
];