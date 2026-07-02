<?php
/*
 * Project:     beacon
 * File:        api.php
 * Date:        2026-06-29
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

/** @var Router $router */

use App\Controllers\UserController;
use App\Routing\Router;

$router->get('/user/me', [UserController::class, 'me'], 'api.user.me');