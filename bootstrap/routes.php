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
$router = $container->make(Router::class);

require base_path('routes/web.php');
