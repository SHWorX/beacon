<?php
/*
 * Project:     Beacon
 * File:        AuthService.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Services;

use App\Enums\LoginResult;
use App\Helpers\StringHelper;
use App\Models\RememberToken;
use App\Models\User;
use App\Support\Cookie;
use App\Support\Session;
use Carbon\Carbon;

final class AuthService
{
    private const string SESSION_KEY = 'auth_user_id';
    private ?User $user = null;

    public function __construct(
        private readonly Session $session,
    ) { }

    /**
     * Attempt email + password
     *
     * @param string $email
     * @param string $password
     *
     * @return bool
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function attempt(string $email, string $password): LoginResult
    {
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            return LoginResult::INVALID_CREDENTIALS;
        }

        // Email address not verified
        if (!$user->hasVerifiedEmail()) {
            return LoginResult::EMAIL_NOT_VERIFIED;
        }

        if (!password_verify($password, $user->password)) {
            return LoginResult::INVALID_CREDENTIALS;
        }

        $this->login($user);

        return LoginResult::SUCCESS;
    }

    /**
     * Login user
     *
     * @param User $user
     *
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function login(User $user): void
    {
        $this->user = $user;
        $this->session->set(self::SESSION_KEY, $user->id);

        // security best practice: prevent session fixation
        session_regenerate_id(true);
    }

    /**
     * Auto-login user via "Remember Me" token
     *
     * @param string $token
     *
     * @return bool
     * @throws \Random\RandomException
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function loginFromRememberToken(string $token): bool
    {
        $record = RememberToken::query()->where(
            'token_hash',
            hash('sha256', $token)
        )->first();

        if ($record === null) {
            return false;
        }

        if (strtotime($record->expires_at) < time()) {
            $record->delete();
            Cookie::delete('remember_token');

            return false;
        }

        $user = User::find($record->user_id);

        if ($user === null) {
            $record->delete();
            Cookie::delete('remember_token');

            return false;
        }

        $this->login($user);

        // Rotate token
        $record->delete();
        $newToken = StringHelper::generateToken();
        $expiry = Carbon::now()->addDays(config('app.cookieLifetimeRememberMe'));
        RememberToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $newToken),
            'expires_at' => $expiry,
        ]);
        Cookie::set('remember_token', $newToken, $expiry->getTimestamp());

        return true;
    }

    /**
     * Logout user
     *
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function logout(): void
    {
        $user = $this->user();

        if ($user !== null) {
            RememberToken::query()
                ->where('user_id', $user->id)
                ->delete();
        }

        $this->user = null;
        $this->session->forget(self::SESSION_KEY);
        Cookie::delete('remember_token');

        session_regenerate_id(true);
    }

    /**
     * Returns the user or null
     *
     * @return User|null
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function user(): ?User
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $id = $this->session->get(self::SESSION_KEY);

        if (!$id) {
            return null;
        }

        $this->user = User::find($id);

        return $this->user;
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Check if user is guest
     *
     * @return bool
     * @author Steffen Haase <shworx.development@gmail.com
     */
    public function guest(): bool
    {
        return !$this->check();
    }
}