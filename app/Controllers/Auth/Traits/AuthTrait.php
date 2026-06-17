<?php
/*
 * Project:     Beacon
 * File:        AuthTrait.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers\Auth\Traits;

use App\Enums\AuthToken;
use App\Exceptions\InvalidEnumException;
use App\Exceptions\MailerException;
use App\Helpers\StringHelper;
use App\Services\MailService;
use Carbon\Carbon;
use Random\RandomException;

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
        string $token
    ): void {
        $url = app_url(route('register.verify', ['token' => $token]));
        $message = $this->view->render('mail/verify_email.twig', [
            'username' => $username,
            'url' => $url,
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

    /**
     * Generates a verification token incl. expiration time
     *
     * @param AuthToken $type
     *
     * @return array
     * @throws InvalidEnumException
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function getToken(AuthToken $type): array
    {
        $token = StringHelper::generateToken();
        if ($type === AuthToken::EMAIL) {
            $tokenExpiration = config('app.email_verification_token_expiry');
        } elseif ($type === AuthToken::PASSWORD) {
            $tokenExpiration = config('app.password_reset_token_expiry');
        } else {
            throw new InvalidEnumException('Unsupported token type');
        }

        $now = Carbon::now();

        if (str_ends_with($tokenExpiration, 'h')) {
            $expirationTime = (int)substr($tokenExpiration, 0, strlen($tokenExpiration) - 1);
            $expiresAt = $now->addHours($expirationTime);
        } elseif (str_ends_with($tokenExpiration, 'd')) {
            $expirationTime = (int)substr($tokenExpiration, 0, strlen($tokenExpiration) - 1);
            $expiresAt = $now->addDays($expirationTime);
        } else {
            $expiresAt = $now->addHours(48);
        }

        return [
            'token' => $token,
            'expires_at' => $expiresAt,
        ];
    }
}