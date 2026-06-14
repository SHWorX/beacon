<?php
/*
 * Project:     Beacon
 * File:        ConsoleServiceProvider.php
 * Date:        2026-06-12
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Console\Kernel;

final readonly class ConsoleServiceProvider extends ServiceProvider
{

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->container->singleton(
            Kernel::class,
            fn () => new Kernel($this->container)
        );
    }
}