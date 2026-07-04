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
use App\Generators\QrCodeGenerator;
use App\Http\Request;
use App\Services\EncryptionService;
use App\Support\Flash;
use App\Support\Redirect;
use App\Support\Session;
use Illuminate\Contracts\Encryption\Encrypter;
use Psr\Log\LoggerInterface;

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
            fn () => new Request(
                $this->container->make(Session::class)
            )
        );

        $this->container->singleton(
            Session::class,
            fn ($container) => new Session(
                $container->make(EncryptionService::class),
                $container->make(LoggerInterface::class)
            )
        );

        $this->container->singleton(
            Flash::class,
            fn () => new Flash()
        );

        $this->container->singleton(
            Redirect::class,
            fn () => new Redirect()
        );

        $this->container->singleton(
            QrCodeGenerator::class,
            fn () => new QrCodeGenerator()
        );
    }
}