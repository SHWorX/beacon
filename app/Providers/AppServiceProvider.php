<?php
/*
 * Project:     Beacon
 * File:        AppServiceProvider.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Container\Container;
use App\Http\Request;
use App\Support\Flash;
use App\Support\Redirect;
use App\Support\Session;

final readonly class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(
            Container::class,
            fn () => $this->container
        );

        $this->container->singleton(
            Request::class,
            fn () => new Request
        );

        $this->container->singleton(
            Session::class,
            fn () => new Session()
        );

        $this->container->singleton(
            Flash::class,
            fn () => new Flash()
        );

        $this->container->singleton(
            Redirect::class,
            fn () => new Redirect()
        );
    }
}