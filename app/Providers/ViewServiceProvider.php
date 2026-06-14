<?php
/*
 * Project:     Beacon
 * File:        ViewServiceProvider.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Routing\Router;
use App\Services\AuthService;
use App\Services\CsrfService;
use App\Support\Flash;
use App\View\AppExtension;
use App\View\View;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

final readonly class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(
            Environment::class,
            function () {
                $loader = new FilesystemLoader(resource_path('views'));

                $twig = new Environment($loader, [
                    'cache' => config('twig.cache', false) !== false ? config('twig.cache') : false,
                    'debug' => config('twig.debug', false),
                ]);

                $twig->addExtension(new AppExtension(
                    $this->container->make(Router::class),
                    $this->container->make(CsrfService::class),
                    $this->container->make(Flash::class),
                    $this->container->make(AuthService::class),
                ));

                if (config('twig.debug', false) === true) {
                    $twig->addExtension(new DebugExtension());
                }

                $twig->addGlobal('auth', auth());

                return $twig;
            }
        );

        $this->container->singleton(
            View::class,
            fn ($container) => new View(
                $container->make(Environment::class),
                $container->make(Flash::class),
                $container->make(LoggerInterface::class)
            )
        );
    }
}