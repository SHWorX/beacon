<?php
/*
 * Project:     beacon
 * File:        RouteDefinition.php
 * Date:        2026-06-30
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Routing;

final class RouteDefinition
{
    /**
     * Constructor
     *
     * @param Router $router
     * @param string $name
     */
    public function __construct(
        private readonly Router $router,
        private readonly string $name,
    ) {}

    /**
     * Add one or more middleware
     *
     * @param array $middleware
     *
     * @return $this
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function middleware(array $middleware): self
    {
        $this->router->addMiddlewareToRoute($this->name, $middleware);

        return $this;
    }

    /**
     * Remove one or more middleware
     *
     * @param array $middleware
     *
     * @return $this
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function withoutMiddleware(array $middleware): self
    {
        $this->router->withoutMiddleware($this->name, $middleware);

        return $this;
    }

    /**
     * Set middleware priority
     *
     * @param array $priority
     *
     * @return $this
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function priority(array $priority): self
    {
        $this->router->setMiddlewarePriority($this->name, $priority);

        return $this;
    }
}
