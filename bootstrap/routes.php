<?php
/*
 * Project:     Beacon
 * File:        routes.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Container\Container;
use App\Routing\Router;

/** @var Container $container */
/** @var Router $router */
$router = $container->make(Router::class);

$router->group(
    prefix: '',
    callback: function (Router $router) {
        require base_path('routes/web.php');
    },
    middleware: ['csrf', 'remember']
);

$router->group(
    prefix: '/api',
    callback: function (Router $router) {
        require base_path('routes/api.php');
    },
    middleware: ['api', 'api_auth', 'throttle:120,60'],
);
