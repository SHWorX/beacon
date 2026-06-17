<?php
/*
 * Project:     Beacon
 * File:        Router.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Routing;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    /** @var RouteCollection  */
    private RouteCollection $routes;

    /** @var array<string,string> */
    private array $namedRoutes = [];

    /** @var string  */
    private string $prefix = '';

    /** @var array<class-string> */
    private array $middlewareStack = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    /**
     * Return all routes
     *
     * @return RouteCollection
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    /**
     * Add a "get" route
     *
     * @param string $path Route path
     * @param array|string|callable $handler Handler
     * @param string|null $name [optional] Route name
     *
     * @return void
     * @throws InvalidArgumentException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function get(
        string $path,
        array|string|callable $handler,
        ?string $name = null,
    ): void {
        $this->addRoute(
            ['GET'],
            $path,
            $handler,
            $name,
        );
    }

    /**
     * Add a "post" route
     *
     * @param string $path Route path
     * @param array|string|callable $handler Handler
     * @param string|null $name [optional] Route name
     *
     * @return void
     * @throws InvalidArgumentException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function post(
        string $path,
        array|string|callable $handler,
        ?string $name = null,
    ): void {
        $this->addRoute(
            ['POST'],
            $path,
            $handler,
            $name,
        );
    }

    /**
     * Add a "put" route
     *
     * @param string $path Route path
     * @param array|string|callable $handler Handler
     * @param string|null $name [optional] Route name
     *
     * @return void
     * @throws InvalidArgumentException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function put(
        string $path,
        array|string|callable $handler,
        ?string $name = null,
    ): void {
        $this->addRoute(
            ['PUT'],
            $path,
            $handler,
            $name,
        );
    }

    /**
     * Add a "delete" route
     *
     * @param string $path Route path
     * @param array|string|callable $handler Handler
     * @param string|null $name [optional] Route name
     *
     * @return void
     * @throws InvalidArgumentException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function delete(
        string $path,
        array|string|callable $handler,
        ?string $name = null,
    ): void {
        $this->addRoute(
            ['DELETE'],
            $path,
            $handler,
            $name,
        );
    }

    /**
     * Adds a new route
     *
     * @param array $methods Route methods
     * @param string $path Route path
     * @param array|string|callable $handler Handler
     * @param string|null $name [optional] Route name
     *
     * @return void
     * @throws InvalidArgumentException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function addRoute(
        array $methods,
        string $path,
        array|string|callable $handler,
        ?string $name = null
    ): void {
        if ($path === '') {
            throw new InvalidArgumentException('Route path cannot be empty.');
        }

        if (!str_starts_with($path, '/')) {
            throw new InvalidArgumentException(sprintf('Route path "%s" must start with "/".', $path));
        }

        $path = $this->prefix . $path;
        $name ??= md5(implode('|', $methods) . $path);

        $route = new Route(
            $path,
            [
                '_handler' => $handler,
                '_middleware' => $this->resolveMiddleware(),
            ]
        );

        $route->setMethods($methods);
        $this->routes->add($name, $route);

        if ($name !== null) {
            $this->namedRoutes[$name] = $path;
        }
    }

    /**
     * Returns a route by name
     *
     * @param string $name Route name
     * @param array $parameters [optional] Route parameters (default: [])
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function route(string $name, array $parameters = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RuntimeException(sprintf('Route [%s] not found.', $name));
        }

        $uri = $this->namedRoutes[$name];

        foreach ($parameters as $key => $value) {
            $uri = str_replace('{' . $key . '}', urlencode((string)$value), $uri);
        }

        return $uri;
    }

    /**
     * Group routes
     *
     * @param callable $callback Callback
     * @param string $prefix Prefix
     * @param array<class-string> $middleware Middleware
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function group(
        string $prefix,
        callable $callback,
        array $middleware = []
    ): void {
        $previous = $this->prefix;
        $this->prefix .= $prefix;
        $this->middlewareStack[] = $middleware;
        $callback($this);
        array_pop($this->middlewareStack);
        $this->prefix = $previous;
    }

    /**
     * Add middleware
     *
     * @param array $middleware
     *
     * @return $this
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function middleware(array $middleware): self
    {
        $this->middlewareStack = $middleware;

        return $this;
    }

    /**
     * Middleware resolver
     *
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function resolveMiddleware(): array
    {
        $middleware = [];

        foreach ($this->middlewareStack as $group) {
            $middleware = array_merge($middleware, $group);
        }

        return array_unique($middleware);
    }
}