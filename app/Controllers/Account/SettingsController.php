<?php
/*
 * Project:     beacon
 * File:        SettingsController.php
 * Date:        2026-06-20
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers\Account;

use App\Controllers\Auth\Traits\AuthTrait;
use App\Controllers\Controller;
use App\DTO\ChangeEmailDto;
use App\DTO\ChangePasswordDto;
use App\DTO\ResendVerificationDto;
use App\Enums\AuthToken;
use App\Exceptions\InvalidEnumException;
use App\Exceptions\MailerException;
use App\Exceptions\ValidationException;
use App\Helpers\StringHelper;
use App\Http\Request;
use App\Http\Response;
use App\Models\RememberToken;
use App\Models\User;
use App\Services\AuthService;
use App\Services\MailService;
use App\Services\ValidationService;
use App\Support\Flash;
use Carbon\Carbon;
use Random\RandomException;

class SettingsController extends Controller
{
    public function index(AuthService $auth): Response {

        return $this->view('account/settings.twig', [
            'email' => $auth->user()->email,
        ]);
    }

    /**
     * @param Request $request
     * @param AuthService $auth
     * @param ValidationService $validator
     * @param MailService $mailer
     *
     * @return Response
     * @throws InvalidEnumException
     * @throws MailerException
     * @throws RandomException
     * @throws ValidationException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function updateEmail(
        Request $request,
        AuthService $auth,
        ValidationService $validator,
        MailService $mailer,
    ): Response {
        $dto = ChangeEmailDto::fromArray($request->all());
        $validator->validate($dto);

        $verificationToken = StringHelper::getVerificationToken(AuthToken::EMAIL);
        $user = $auth->user();

        $updated = $user->update([
            'email' => $dto->email,
            'email_verified_at' => null,
            'email_verification_token' => hash('sha256', $verificationToken['token']),
            'email_verification_expires_at' => $verificationToken['expires_at'],
        ]);

        if (!$updated) {
            $this->logger->error('Failed to update user during email change.');
            $data = [
                'title' => 'HTTP 500 - Server Error',
                'content' => '<p class="pt-sm">Cannot update email address, please try again later.<br>' .
                    'Please contact the webmaster in case the error persists.</p>',
            ];

            return $this->view('common_notification.twig', $data);
        }

        $this->sendVerificationEmail($mailer, $user->username, $dto->email, $verificationToken['token']);

        $data = [
            'title' => 'Settings - Change email',
            'content' => '<p class="pt-sm">Your email address has been updated.<br>To finish the email update process, ' .
                'we have sent you a verification email to your new email address.<br>Please check your inbox and click ' .
                'on the link in the email.</p>',
        ];

        return $this->view('common_notification.twig', $data);
    }

    /**
     * Verify email address
     *
     * @param AuthService $auth
     * @param $token
     *
     * @return Response
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function verifyEmail(
        AuthService $auth,
        $token,
    ): Response {
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
                    '<a href="' .route('settings.resend-verification-form') . '" ' .
                    'class="btn btn-primary shadow-sm mt-sm">Resend Verification Email</a>',
            ];

            return $this->view('common_notification.twig', $data);
        }

        if ($user->hasVerifiedEmail()) {
            $data = [
                'title' => 'Email Verification',
                'content' => '<p class="pt-sm">This email address has already been verified.</p>' .
                    '<a href="' . route('login') . '" class="btn btn-primary shadow-sm mt-sm">Go to Login</a>',
            ];

            return $this->view('common_notification.twig', $data);
        }

        $user->markEmailAsVerified();

        if ($auth->check()) {
            $auth->logout();
        }

        $data = [
            'title' => 'Email Verification',
            'content' => '<p class="pt-sm">Your email address has been successfully verified.</p>' .
                '<a href="' . route('login') . '" class="btn btn-primary shadow-sm mt-sm">Go to Login</a>',
        ];

        return $this->view('common_notification.twig', $data);
    }

    public function showResendVerificationForm(): Response
    {
        return $this->view('account/resend_verification_form.twig');
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
                return Response::redirect(route('account.resend-verification-form'));
            }

            $verificationToken = StringHelper::getVerificationToken(AuthToken::EMAIL);

            $user->update([
                'email_verification_token' => hash('sha256', $verificationToken['token']),
                'email_verification_expires_at' => $verificationToken['expires_at'],
            ]);

            $this->sendVerificationEmail($mailer, $user->username, $user->email, $verificationToken['token']);
        }

        $data = [
            'title' => 'Email Verification',
            'content' => '<p class="pt-sm">If an account exists for this email, an email verification link has been sent.</p>',
        ];

        return $this->view('common_notification.twig', $data);
    }

    public function updatePassword(
        Request $request,
        AuthService $auth,
        ValidationService $validator,
        Flash $flash,
    ): Response {
        $user = $auth->user();

        if (!password_verify($request->post('password_current'), $user->password)) {
            $flash->error(['password_current' => 'Your current password is incorrect.']);
            return Response::redirect(route('account.settings'));
        }

        $dto = ChangePasswordDto::fromArray($request->all());
        $validator->validate($dto);

        $user->password = password_hash($dto->password, PASSWORD_BCRYPT);
        $user->save();

        // Logout all remembered devices after password reset
        RememberToken::query()->where('user_id', $user->id)->delete();

        $data = [
            'title' => 'Settings - Change password',
            'content' => '<p class="pt-sm">Your account password has been changed.</p>',
        ];

        return $this->view('common_notification.twig', $data);
    }

    /**
     * @param MailService $mailer
     * @param string $username
     * @param string $email
     * @param string $token
     *
     * @return void
     * @throws MailerException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function sendVerificationEmail(
        MailService $mailer,
        string $username,
        string $email,
        string $token,
    ): void {
        $message = $this->view->render('mail/verify_changed_email.twig', [
            'username' => $username,
            'url' => app_url(route('settings.verify', ['token' => $token]))
        ]);

        $mailer->send(
            $email,
            'Verify your email address',
            $message,
            true,
        );
    }
}
