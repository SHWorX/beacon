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
use App\Helpers\StringHelper;
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
     * @author Steffen Haase <shworx.development@gmail.com>
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

        $verificationToken = StringHelper::getVerificationToken(AuthToken::EMAIL);

        $user = User::create([
            'username' => $dto->username,
            'email' => $dto->email,
            'password' => password_hash($dto->password, PASSWORD_BCRYPT),
            'email_verification_token' => hash('sha256', $verificationToken['token']),
            'email_verification_expires_at' => $verificationToken['expires_at'],
        ]);

        $this->sendVerificationEmail($mailer, $dto->username, $dto->email, $verificationToken['token']);

        $data = [
            'title' => 'Verify your email address',
            'content' => '<p class="pt-sm">Your account as been successfully created.<br>To finish the registration, ' .
                'we have sent you a verification email.<br>Please check your inbox and click on the link in the email.</p>',
        ];

        return $this->view('common_notification.twig', $data);
    }

}
