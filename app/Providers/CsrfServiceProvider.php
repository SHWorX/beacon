<?php
/*
 * Project:     Beacon
 * File:        CsrfServiceProvider.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Services\CsrfService;

final readonly class CsrfServiceProvider extends ServiceProvider
{

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->container->singleton(
            CsrfService::class,
            fn () => new CsrfService()
        );
    }
}