<?php
/*
 * Project:     Beacon
 * File:        ForgottenPasswordController.php
 * Date:        2026-06-13
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers\Auth;

use App\Controllers\Auth\Traits\AuthTrait;
use App\Controllers\Controller;
use App\DTO\ForgottenPasswordDto;
use App\DTO\PasswordResetDto;
use App\Enums\AuthToken;
use App\Exceptions\InvalidEnumException;
use App\Exceptions\MailerException;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Models\PasswordResetToken;
use App\Models\RememberToken;
use App\Models\User;
use App\Services\MailService;
use App\Services\ValidationService;
use App\Support\Flash;
use Carbon\Carbon;
use Random\RandomException;

class ForgottenPasswordController extends Controller
{
    use AuthTrait;

    /**
     * Show forgotten password form
     *
     * @return Response
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    public function index(): Response
    {
        return $this->view('auth/forgotten_password.twig');
    }

    /**
     * Send a password reset email
     *
     * @param Request $request
     * @param MailService $mailer
     * @param ValidationService $validator
     * @param Flash $flash
     *
     * @return Response
     * @throws InvalidEnumException
     * @throws MailerException
     * @throws ValidationException
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function sendResetEmail(
        Request $request,
        MailService $mailer,
        ValidationService $validator,
        Flash $flash,
    ): Response {
        $dto = ForgottenPasswordDto::fromArray($request->all());
        $validator->validate($dto);

        $user = User::query()->where('email', $dto->email)->first();

        if ($user !== null) {
            if (!$user->hasVerifiedEmail()) {
                $flash->error([
                    'verification' => 'Please verify your email address first.<br>' .
                        '<a href="' . route('register.resend-verification-form') . '">Resend verification email</a>'
                ]);

                return Response::redirect(route('forgotten'));
            }


            $token = $this->getToken(AuthToken::PASSWORD);

            PasswordResetToken::query()->where('user_id', $user->id)->delete();
            PasswordResetToken::query()->create([
                'user_id' => $user->id,
                'token_hash' => hash('sha256', $token['token']),
                'expires_at' => $token['expires_at'],
            ]);

            $this->sendResetPasswordEmail($mailer, $user->username, $user->email, $token['token']);
        }

        $data = [
            'title' => 'Password Reset',
            'content' => '<p class="pt-sm">If an account exists for this email, a password reset link has been sent.</p>',
        ];
        return $this->view('auth/common_notification.twig', $data);
    }

    /**
     * Show password reset form
     *
     * @param $token
     *
     * @return Response
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function reset($token): Response
    {
        if (!$token) {
            return $this->view('errors/404.twig', [], 404);
        }

        $tokenHash = hash('sha256', $token);

        $prt = PasswordResetToken::query()
            ->where('token_hash', $tokenHash)
            ->first();

        if ($prt === null) {
            return $this->view('errors/404.twig', [], 404);
        }

        if ($prt->expires_at < Carbon::now()) {
            $data = [
                'title' => 'Password Reset',
                'content' => '<p class="pt-sm">This password reset has expired.</p>' .
                    '<a href="' . route('forgotten') .
                    '" class="btn btn-primary shadow-sm mt-sm">Request new password reset</a>',
            ];

            return $this->view('auth/common_notification.twig', $data);
        }

        return $this->view('auth/reset_password.twig', ['token' => $token]);
    }

    /**
     * Process password reset form submission
     *
     * @param Request $request
     * @param ValidationService $validator
     *
     * @return Response
     * @throws ValidationException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function resetPassword(
        Request $request,
        ValidationService $validator,
    ): Response {
        $dto = PasswordResetDto::fromArray($request->all());
        $validator->validate($dto);

        $prt = PasswordResetToken::with('user')
            ->where('token_hash', hash('sha256', $dto->token))
            ->first();

        if ($prt === null) {
            return $this->view('errors/404.twig', [], 404);
        }

        $user = $prt->user;
        $user->password = password_hash($dto->password, PASSWORD_BCRYPT);
        $user->save();

        $prt->delete();

        // Logout all remembered devices after password reset
        RememberToken::query()->where('user_id', $user->id)->delete();

        $data = [
            'title' => 'Password Reset',
            'content' => '<p class="pt-sm">Your account password has been reset.</p>' .
                '<a href="' . route('login') . '" class="btn btn-primary shadow-sm mt-sm">Go to Login</a>'
        ];
        return $this->view('auth/common_notification.twig', $data);
    }
}
