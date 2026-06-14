<?php
/*
 * Project:     Beacon
 * File:        web.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Controllers\Auth\ForgottenPasswordController;
use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\LogoutController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Middleware\AuthMiddleware;
use App\Routing\Router;

/** @var Router $router */

$router->get('/', [HomeController::class, 'index'], 'home');
$router->get('/about', [HomeController::class, 'about'], 'about');

$router->group(
    prefix: '/auth',
    callback: function (Router $router) {
        $router->get('/login', [LoginController::class, 'index'], 'login');
        $router->post('/login', [LoginController::class, 'login'], 'login.post');
        $router->get('/logout', [LogoutController::class, 'logout'], 'logout');

        $router->get('/register', [RegisterController::class, 'index'], 'register');
        $router->post('/register', [RegisterController::class, 'register'], 'register.post');

        $router->get('/verify/{token}', [RegisterController::class, 'verifyEmail'], 'register.verify');
        $router->get('/resend_verification', [RegisterController::class, 'showResendVerificationForm'], 'register.resend-verification-form');
        $router->post('/resend-verification', [RegisterController::class, 'resendVerificationEmail'], 'register.resend-verification');

        $router->get('/forgotten', [ForgottenPasswordController::class, 'index'], 'forgotten');
        $router->post('/forgotten', [ForgottenPasswordController::class, 'sendResetEmail'], 'forgotten.post');

        $router->get('/reset/{token}', [ForgottenPasswordController::class, 'reset'], 'reset.token');
        $router->post('/reset', [ForgottenPasswordController::class, 'resetPassword'], 'reset.post');
    }
);

$router->group(
    prefix: '',
    callback: function (Router $router) {
        $router->get('/dashboard', [DashboardController::class, 'index'], 'dashboard');
    },
    middleware: [AuthMiddleware::class]
);
