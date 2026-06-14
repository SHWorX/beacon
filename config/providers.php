<?php
/*
 * Project:     Beacon
 * File:        providers.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\ConsoleServiceProvider;
use App\Providers\CsrfServiceProvider;
use App\Providers\DatabaseProvider;
use App\Providers\LoggingServiceProvider;
use App\Providers\MailServiceProvider;
use App\Providers\RoutingServiceProvider;
use App\Providers\ValidationServiceProvider;
use App\Providers\ViewServiceProvider;

return [
    AppServiceProvider::class,
    DatabaseProvider::class,
    ConsoleServiceProvider::class,
    RoutingServiceProvider::class,
    ViewServiceProvider::class,
    LoggingServiceProvider::class,
    MailServiceProvider::class,
    CsrfServiceProvider::class,
    ValidationServiceProvider::class,
    AuthServiceProvider::class,
];