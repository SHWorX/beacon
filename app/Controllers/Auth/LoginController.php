<?php
/*
 * Project:     Beacon
 * File:        LoginController.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\DTO\LoginDto;
use App\Enums\LoginResult;
use App\Exceptions\ValidationException;
use App\Helpers\StringHelper;
use App\Http\Request;
use App\Http\Response;
use App\Models\RememberToken;
use App\Services\AuthService;
use App\Services\ValidationService;
use App\Support\Cookie;
use App\Support\Flash;
use Carbon\Carbon;
use Random\RandomException;

class LoginController extends Controller
{
    /**
     * Show login form
     *
     * @return Response
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    public function index(): Response
    {
        return $this->view('auth/login.twig');
    }

    /**
     * Process login form submission
     *
     * @param Request $request
     * @param ValidationService $validator
     * @param AuthService $auth
     * @param Flash $flash
     *
     * @return Response
     * @throws ValidationException
     * @throws RandomException
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    public function login(
        Request $request,
        ValidationService $validator,
        AuthService $auth,
        Flash $flash,
    ): Response {
        $dto = LoginDto::fromArray($request->all());
        $validator->validate($dto);

        $result = $auth->attempt($dto->email, $dto->password);

        switch ($result) {
            case LoginResult::EMAIL_NOT_VERIFIED:
                $flash->set('errors', [
                    'auth_error' => 'Please verify your email address.<br>' .
                        '<a href="' . route('register.resend-verification-form') . '">Resend verification email</a>'
                ]);

                return Response::redirect(route('login'));
            case LoginResult::INVALID_CREDENTIALS:
                $flash->set('errors', ['auth_error' => 'Invalid email or password.']);

                return Response::redirect(route('login'));
        }

        if ($dto->remember) {
            $token = StringHelper::generateToken();
            $expiry = Carbon::now()->addDays(config('app.cookieLifetimeRememberMe'));
            RememberToken::create([
                'user_id' => $auth->user()->id,
                'token_hash' => hash('sha256', $token),
                'expires_at' => $expiry,
            ]);
            Cookie::set('remember_token', $token, $expiry->getTimestamp());
        }

        return Response::redirect(route('dashboard'));
    }
}
