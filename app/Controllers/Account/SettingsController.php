<?php
/*
 * Project:     beacon
 * File:        SettingsController.php
 * Date:        2026-06-20
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers\Account;

use App\Controllers\Controller;
use App\DTO\ChangeEmailDto;
use App\DTO\ChangePasswordDto;
use App\DTO\DisableTwoFactorDto;
use App\DTO\EnableTwoFactorDto;
use App\Enums\AuthToken;
use App\Exceptions\InvalidEnumException;
use App\Exceptions\MailerException;
use App\Exceptions\ValidationException;
use App\Helpers\StringHelper;
use App\Http\Request;
use App\Http\Response;
use App\Models\RememberToken;
use App\Services\AuthService;
use App\Services\MailService;
use App\Services\TwoFactorService;
use App\Services\ValidationService;
use App\Support\Flash;
use JsonException;
use Random\RandomException;

class SettingsController extends Controller
{
    public function index(AuthService $auth): Response
    {
        return $this->view('account/settings.twig', [
            'email' => $auth->user()->email,
            'two_factor_enabled' => $auth->user()->totp_enabled_at !== null,
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
            'title' => 'Verify you email address',
            'content' => '<p class="pt-sm">Your email address has been updated.<br>To finish the email update process, ' .
                'we have sent you a verification email to your new email address.<br>Please check your inbox and click ' .
                'on the link in the email.</p>',
        ];

        return $this->view('common_notification.twig', $data);
    }

    /**
     * @param Request $request
     * @param AuthService $auth
     * @param ValidationService $validator
     * @param Flash $flash
     *
     * @return Response
     * @throws ValidationException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
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
     * @param Request $request
     * @param TwoFactorService $twoFactor
     *
     * @return Response
     * @throws RandomException
     * @throws JsonException
     * @throws \Endroid\QrCode\Exception\ValidationException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function setupTwoFactor(Request $request, TwoFactorService $twoFactor): Response
    {
        $setup = $twoFactor->beginSetup($this->session);

        return Response::json([
            'setup_id' => $setup->id,
            'secret' => $setup->secret,
            'qr' => $twoFactor->getQrCodeSvg(auth()->user(), $setup),
        ]);
    }

    /**
     * @param Request $request
     * @param TwoFactorService $twoFactor
     * @param Flash $flash
     *
     * @return Response
     * @throws JsonException
     * @throws RandomException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function enableTwoFactor(Request $request, TwoFactorService $twoFactor, Flash $flash): Response
    {
        $dto = EnableTwoFactorDto::fromArray($request->all());

        $setup = $twoFactor->pendingSetup($this->session);
        if ($setup === null) {
            $flash->error(['totp_setup' => 'Your setup session has expired.']);
            return $this->view('account/settings.twig');
        }

        if ($setup->id !== $dto->setup_id) {
            $flash->error(['totp_setup' => 'Invalid setup session.']);
            return $this->view('account/settings.twig');
        }

        if (!$twoFactor->verifySecret($setup->secret, $dto->code)) {
            $flash->error(['totp_setup' => 'Invalid authentication code.']);
            return $this->view('account/settings.twig');
        }

        $recoveryCodes = $twoFactor->enable($this->auth->user(), $setup);

        $flash->success(['recovery_codes', $recoveryCodes]);
        return $this->view('account/settings.twig');
    }

    public function disableTwoFactor(Request $request, TwoFactorService $twoFactor, Flash $flash): Response {
        $dto = DisableTwoFactorDto::fromArray($request->all());
        $user = $this->auth->user();

        if (!password_verify($dto->password, $user->password)) {
            $flash->error(['totp_setup', 'Password is incorrect.']);
            return $this->view('account/settings.twig');
        }

        $twoFactor->disable($user);

        $flash->success(['totp_setup', 'Two-factor authentication has been disabled.']);
        return $this->view('account/settings.twig');
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
        $message = $this->view->render('mail/verify_email.twig', [
            'username' => $username,
            'url' => app_url(route('auth.verify', ['token' => $token])),
        ]);

        $mailer->send(
            $email,
            'Verify your email address',
            $message,
            true,
        );
    }
}
