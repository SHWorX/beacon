<?php
/*
 * Project:     Beacon
 * File:        RoutingServiceProvider.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Container\Resolver;
use App\Http\Kernel;
use App\Routing\RouteDispatcher;
use App\Routing\Router;
use App\Services\CsrfService;
use App\Support\Flash;
use App\View\View;
use Psr\Log\LoggerInterface;

final readonly class RoutingServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->container->singleton(
            Resolver::class,
            fn () => new Resolver($this->container)
        );

        $this->container->singleton(
            Router::class,
            fn () => new Router()
        );

        $this->container->singleton(
            RouteDispatcher::class,
            fn () => new RouteDispatcher(
                $this->container->make(Router::class),
                $this->container,
                $this->container->make(Resolver::class)
            )
        );

        $this->container->singleton(
            Kernel::class,
            fn () => new Kernel(
                $this->container,
                $this->container->make(RouteDispatcher::class),
                $this->container->make(Flash::class),
                $this->container->make(CsrfService::class),
                $this->container->make(View::class),
                $this->container->make(LoggerInterface::class)
            )
        );
    }
}