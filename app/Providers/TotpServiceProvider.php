<?php
/*
 * Project:     beacon
 * File:        TotpServiceProvider.php
 * Date:        2026-07-02
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Generators\RecoveryCodeGenerator;
use App\Generators\TotpSecretGenerator;
use App\Providers\ServiceProvider;
use App\Security\Totp;
use App\Services\EncryptionService;
use App\Services\TwoFactorService;

readonly class TotpServiceProvider extends ServiceProvider
{

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->container->singleton(
            EncryptionService::class,
            fn() => new EncryptionService()
        );

        $this->container->singleton(
            Totp::class,
            fn() => new Totp(
                digits: config('twofactor.digits'),
                period: config('twofactor.period'),
                window: config('twofactor.window'),
            )
        );

        $this->container->singleton(
            TwoFactorService::class,
            fn ($container) => new TwoFactorService(
                $container->make(Totp::class),
                $container->make(EncryptionService::class),
                $container->make(TotpSecretGenerator::class),
                $container->make(RecoveryCodeGenerator::class)
            )
        );
    }
}
