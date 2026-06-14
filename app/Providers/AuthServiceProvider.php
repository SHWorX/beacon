<?php
/*
 * Project:     Beacon
 * File:        AuthServiceProvider.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Providers\ServiceProvider;
use App\Services\AuthService;
use App\Support\Session;

final readonly class AuthServiceProvider extends ServiceProvider
{

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->container->singleton(
            AuthService::class,
            fn () => new AuthService(
                $this->container->make(Session::class)
            )
        );
    }
}