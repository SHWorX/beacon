<?php
/*
 * Project:     Beacon
 * File:        ValidationServiceProvider.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Services\ValidationService;

final readonly class ValidationServiceProvider extends ServiceProvider
{

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->container->singleton(
            ValidationService::class,
            fn () => new ValidationService()
        );
    }
}