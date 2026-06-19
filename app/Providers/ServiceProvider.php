<?php
/*
 * Project:     Beacon
 * File:        ServiceProvider.php
 * Date:        2026-06-09
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Providers;

use App\Container\Container;
use ReflectionException;

abstract readonly class ServiceProvider
{
    public function __construct(
        protected Container $container,
    ) { }

    /**
     * @return array
     * @throws ReflectionException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    protected function config(): array
    {
        return $this->container->make('config');
    }

    /**
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    abstract public function register(): void;

    /**
     * @return void
     * @author Steffen Haase <shworx.development@gmail.com>
     */
    public function boot(): void
    {

    }
}