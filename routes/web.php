<?php
/*
 * Project:     Beacon
 * File:        web.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

use App\Controllers\Account\SettingsController;
use App\Controllers\Auth\ForgottenPasswordController;
use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\LogoutController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\VerifyEmailController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Middleware\AuthMiddleware;
use App\Routing\Router;

/** @var Router $router */

/* Landing page */
$router->get('/', [HomeController::class, 'index'], 'home');

/* Email Verification routes should not require auth or guest middleware, because verifications emails will be sent out
 * during registration, and during email change in settings.
 */
$router->group(
    prefix: '',
    callback: function (Router $router) {
        $router->get('/verify/{token}', [VerifyEmailController::class, 'verifyEmail'], 'auth.verify');
        $router->get('/resend-verification', [VerifyEmailController::class, 'showResendVerificationForm'], 'auth.verification.resend');
        $router->post('/resend-verification', [VerifyEmailController::class, 'resendVerificationEmail'], 'auth.verification.resend.post')
            ->middleware(['throttle:5,1']);
    }
);

/* Registration, Login/Logout, Password reset, and email verification routes */
$router->group(
    prefix: '/auth',
    callback: function (Router $router) {
        $router->get('/login', [LoginController::class, 'index'], 'auth.login');
        $router->post('/login', [LoginController::class, 'login'], 'auth.login.post');

        $router->get('/register', [RegisterController::class, 'index'], 'auth.register');
        $router->post('/register', [RegisterController::class, 'register'], 'auth.register.post');

        $router->get('/forgotten', [ForgottenPasswordController::class, 'index'], 'auth.forgotten');
        $router->post('/forgotten', [ForgottenPasswordController::class, 'sendResetEmail'], 'auth.forgotten.post');

        $router->get('/reset/{token}', [ForgottenPasswordController::class, 'reset'], 'auth.reset.token');
        $router->post('/reset', [ForgottenPasswordController::class, 'resetPassword'], 'auth.reset.post');
    },
    middleware: ['guest']
);

/* All routes which requires authentication */
$router->group(
    prefix: '',
    callback: function (Router $router) {
        $router->get('/logout', [LogoutController::class, 'logout'], 'logout');

        $router->group(
            prefix: '',
            callback: function (Router $router) {
                $router->get('/dashboard', [DashboardController::class, 'index'], 'dashboard');
            },
        );

        /* Account Settings routes */
        $router->group(
            prefix: '/account',
            callback: function (Router $router) {
                $router->get('/settings', [SettingsController::class, 'index'], 'account.settings');
                $router->post('/settings/email', [SettingsController::class, 'updateEmail'], 'account.settings.email.post')
                    ->middleware(['throttle:5,1']);
                $router->post('/settings/password', [SettingsController::class, 'updatePassword'], 'account.settings.password.post');

                /* Two-factor authentication routes */
                $router->post('/settings/two-factor/setup', [SettingsController::class, 'setupTwoFactor'], 'account.settings.twofactor.setup');
                $router->post('/settings/two-factor/enable', [SettingsController::class, 'enableTwoFactor'], 'account.settings.twofactor.enable');
                $router->post('/settings/two-factor/disable', [SettingsController::class, 'disableTwoFactor'], 'account.settings.twofactor.disable');
            },
        );

    },
    middleware: ['auth']
);
