<?php
/*
 * Project:     beacon
 * File:        VerifyEmailController.php
 * Date:        2026-07-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers\Auth;

use App\Controllers\Auth\Traits\AuthTrait;
use App\Controllers\Controller;
use App\DTO\ResendVerificationDto;
use App\Enums\AuthToken;
use App\Exceptions\InvalidEnumException;
use App\Exceptions\MailerException;
use App\Exceptions\ValidationException;
use App\Helpers\StringHelper;
use App\Http\Request;
use App\Http\Response;
use App\Models\User;
use App\Services\AuthService;
use App\Services\MailService;
use App\Services\ValidationService;
use App\Support\Flash;
use Carbon\Carbon;
use Random\RandomException;

class VerifyEmailController extends Controller
{
    use AuthTrait;

    /**
     * Verify email address
     *
     * @param $token
     *
     * @return Response
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function verifyEmail($token): Response
    {
        if (!$token) {
            return $this->view('errors/404.twig', [], 404);
        }

        $tokenHash = hash('sha256', $token);

        $user = User::query()->where('email_verification_token', $tokenHash)->first();
        if (!$user) {
            return $this->view('errors/404.twig', [], 404);
        }

        if ($user->email_verification_expires_at < Carbon::now()) {
            $data = [
                'title' => 'Email Verification',
                'content' => '<p class="pt-sm">This email verification has expired.</p>' .
                    '<a href="' .route('auth.verification.resend') . '" ' .
                    'class="btn btn-primary shadow-sm mt-sm">Resend Verification Email</a>',
            ];

            return $this->view('common_notification.twig', $data);
        }

        if ($user->hasVerifiedEmail()) {
            $data = [
                'title' => 'Email Verification',
                'content' => '<p class="pt-sm">This email address has already been verified.</p>' .
                    '<a href="' . route('auth.login') . '" class="btn btn-primary shadow-sm mt-sm">Go to Login</a>',
            ];

            return $this->view('common_notification.twig', $data);
        }

        $user->markEmailAsVerified();

        $content = '<p class="pt-sm">Your email address has been successfully verified.</p>';
        if ($this->auth->guest()) {
            $content .= '<a href="' . route('auth.login') . '" class="btn btn-primary shadow-sm mt-sm">Go to Login</a>';
        }

        $data = [
            'title' => 'Email Verification',
            'content' => $content,
        ];
        return $this->view('common_notification.twig', $data);
    }

    public function showResendVerificationForm(): Response
    {
        return $this->view('auth/resend_verification_form.twig');
    }

    /**
     * Resend the verification email
     *
     * @param Request $request
     * @param MailService $mailer
     * @param ValidationService $validator
     * @param Flash $flash
     *
     * @return Response
     * @throws MailerException
     * @throws RandomException
     * @throws ValidationException
     * @throws InvalidEnumException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function resendVerificationEmail (
        Request $request,
        MailService $mailer,
        ValidationService $validator,
        Flash $flash,
    ): Response {
        $dto = ResendVerificationDto::fromArray($request->all());
        $validator->validate($dto);

        $user = User::query()->where('email', $dto->email)->first();

        if ($user) {
            if ($user->hasVerifiedEmail()) {
                $flash->error(['verification' => 'This email address is already verified.']);
                return Response::redirect(route('auth.email.resend'));
            }

            $verificationToken = StringHelper::getVerificationToken(AuthToken::EMAIL);

            $user->update([
                'email_verification_token' => hash('sha256', $verificationToken['token']),
                'email_verification_expires_at' => $verificationToken['expires_at'],
            ]);

            $this->sendVerificationEmail($mailer, $user->username, $user->email, $verificationToken['token']);
        }

        $data = [
            'title' => 'System message',
            'content' => '<p class="pt-sm">If an account exists for this email, an email verification link has been sent.</p>',
        ];

        return $this->view('common_notification.twig', $data);
    }
}
