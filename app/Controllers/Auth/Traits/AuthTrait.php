<?php
/*
 * Project:     Beacon
 * File:        AuthTrait.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers\Auth\Traits;

use App\Exceptions\MailerException;
use App\Services\MailService;

trait AuthTrait
{
    /**
     * Send the verification email
     *
     * @param MailService $mailer
     * @param string $username
     * @param string $email
     * @param string $token
     *
     * @return void
     * @throws MailerException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function sendVerificationEmail(
        MailService $mailer,
        string $username,
        string $email,
        string $token,
    ): void {
        $message = $this->view->render('mail/verify_email.twig', [
            'username' => $username,
            'url' => app_url(route('register.verify', ['token' => $token]))
        ]);

        $mailer->send(
            $email,
            'Verify your email address',
            $message,
            true,
        );
    }

    /**
     * Send a password reset email
     *
     * @param MailService $mailer
     * @param string $username
     * @param string $email
     * @param string $token
     *
     * @return void
     * @throws MailerException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function sendResetPasswordEmail(
        MailService $mailer,
        string $username,
        string $email,
        string $token
    ): void {
        $url = app_url(route('reset.token', ['token' => $token]));
        $message = $this->view->render('mail/reset_password.twig', [
            'username' => $username,
            'url' => $url,
        ]);

        $mailer->send(
            $email,
            'Password reset request',
            $message,
            true,
        );
    }
}