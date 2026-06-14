<?php
/*
 * Project:     Beacon
 * File:        RegisterController.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers\Auth;

use App\Controllers\Auth\Traits\AuthTrait;
use App\Controllers\Controller;
use App\DTO\RegisterDto;
use App\DTO\ResendVerificationDto;
use App\Enums\AuthToken;
use App\Exceptions\InvalidEnumException;
use App\Exceptions\MailerException;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Models\User;
use App\Services\MailService;
use App\Services\ValidationService;
use App\Support\Flash;
use Carbon\Carbon;
use Random\RandomException;

class RegisterController extends Controller
{
    use AuthTrait;

    /**
     * Show registration form
     *
     * @return Response
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function index(): Response
    {
        return $this->view('auth/register.twig');
    }

    /**
     * Process form submission
     *
     * @param Request $request
     * @param ValidationService $validator
     * @param MailService $mailer
     *
     * @return Response
     * @throws MailerException
     * @throws RandomException
     * @throws ValidationException
     * @throws InvalidEnumException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function register(
        Request $request,
        ValidationService $validator,
        MailService $mailer,
    ): Response {
        $dto = RegisterDto::fromArray($request->all());
        $validator->validate($dto);

        $verificationToken = $this->getToken(AuthToken::EMAIL);

        $user = User::create([
            'username' => $dto->username,
            'email' => $dto->email,
            'password' => password_hash($dto->password, PASSWORD_BCRYPT),
            'email_verification_token' => hash('sha256', $verificationToken['token']),
            'email_verification_expires_at' => $verificationToken['expires_at'],
        ]);

        $this->sendVerificationEmail($mailer, $dto->username, $dto->email, $verificationToken['token']);

        $data = [
            'title' => 'Registration',
            'content' => '<p class="pt-sm">Your account as been successfully created.<br>To finish the registration, ' .
                'we have sent you a verification email.<br>Please check your inbox and click on the link in the email.</p>',
        ];

        return $this->view('auth/common_notification.twig', $data);
    }

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
                    '<a href="' .route('register.resend-verification-form') . '" ' .
                    'class="btn btn-primary shadow-sm mt-sm">Resend Verification Email</a>',
            ];

            return $this->view('auth/common_notification.twig', $data);
        }

        if ($user->hasVerifiedEmail()) {
            $data = [
                'title' => 'Email Verification',
                'content' => '<p class="pt-sm">This email address has already been verified.</p>' .
                    '<a href="' . route('login') . '" class="btn btn-primary shadow-sm mt-sm">Go to Login</a>',
            ];

            return $this->view('auth/common_notification.twig', $data);
        }

        $user->markEmailAsVerified();

        $data = [
            'title' => 'Email Verification',
            'content' => '<p class="pt-sm">Your email address has been successfully verified.</p>' .
                '<a href="' . route('login') . '" class="btn btn-primary shadow-sm mt-sm">Go to Login</a>',
        ];
        return $this->view('auth/common_notification.twig', $data);
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
    ): Response
    {
        $dto = ResendVerificationDto::fromArray($request->all());
        $validator->validate($dto);

        $user = User::query()->where('email', $dto->email)->first();

        if ($user) {
            if ($user->hasVerifiedEmail()) {
                $flash->error(['verification' => 'This email address is already verified.']);
                return Response::redirect(route('register.resend-verification-form'));
            }

            $verificationToken = $this->getToken(AuthToken::EMAIL);

            $user->update([
                'email_verification_token' => hash('sha256', $verificationToken['token']),
                'email_verification_expires_at' => $verificationToken['expires_at'],
            ]);

            $this->sendVerificationEmail($mailer, $user->username, $user->email, $verificationToken['token']);
        }

        $data = [
            'title' => 'Email Verification',
            'content' => '<p class="pt-sm">If an account exists for this email, an email verification link has been sent.</p>'
        ];

        return $this->view('auth/common_notification.twig', $data);
    }
}
