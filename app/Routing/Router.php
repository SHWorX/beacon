<?php
/*
 * Project:     Beacon
 * File:        Router.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Routing;

use App\Middleware\ApiMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
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

    /** @var array<int, array<class-string>> */
    private array $groupMiddlewareStack = [];

    private array $routeMiddlewareConfig = [];

    /** @var array<string, array> */
    private array $routeMiddlewareMeta = [];

    /** @var array<string, class-string> */
    private array $middlewareMap = [];


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->middlewareMap = require_once config_path('middleware.php');
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
     * @param string $path                   Route path
     * @param array|string|callable $handler Handler
     * @param string|null $name              [optional] Route name
     *
     * @return RouteDefinition
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function get(
        string $path,
        array|string|callable $handler,
        ?string $name = null,
    ): RouteDefinition {
        return $this->addRoute(
            ['GET'],
            $path,
            $handler,
            $name,
        );
    }

    /**
     * Add a "post" route
     *
     * @param string $path                   Route path
     * @param array|string|callable $handler Handler
     * @param string $name                   Route name
     *
     * @return RouteDefinition
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function post(
        string $path,
        array|string|callable $handler,
        string $name
    ): RouteDefinition {
        return $this->addRoute(
            ['POST'],
            $path,
            $handler,
            $name,
        );
    }

    /**
     * Add a "put" route
     *
     * @param string $path                   Route path
     * @param array|string|callable $handler Handler
     * @param string $name                   Route name
     *
     * @return RouteDefinition
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function put(
        string $path,
        array|string|callable $handler,
        string $name
    ): RouteDefinition {
        return $this->addRoute(
            ['PUT'],
            $path,
            $handler,
            $name,
        );
    }

    /**
     * Add a "delete" route
     *
     * @param string $path                   Route path
     * @param array|string|callable $handler Handler
     * @param string $name                   Route name
     *
     * @return RouteDefinition
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function delete(
        string $path,
        array|string|callable $handler,
        string $name
    ): RouteDefinition {
        return $this->addRoute(
            ['DELETE'],
            $path,
            $handler,
            $name,
        );
    }

    /**
     * Adds a new route
     *
     * @param array $methods                 Route methods
     * @param string $path                   Route path
     * @param array|string|callable $handler Handler
     * @param string $name                   Route name
     *
     * @return RouteDefinition
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function addRoute(
        array $methods,
        string $path,
        array|string|callable $handler,
        string $name
    ): RouteDefinition {
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
                '_route' => $name,
                '_middleware' => $this->resolveMiddleware(),
                '_middleware_extra' => [],
                '_middleware_except' => [],
                '_middleware_priority' => [],
            ]
        );

        $route->setMethods($methods);
        $this->routes->add($name, $route);

        $this->routeMiddlewareMeta[$name] = [
            'extra' => [],
            'except' => [],
            'priority' => [],
        ];

        if ($name !== null) {
            $this->namedRoutes[$name] = $path;
        }

        return new RouteDefinition($this, $name);
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
        $previousPrefix = $this->prefix;
        $this->prefix .= $prefix;
        $this->groupMiddlewareStack[] = $middleware;
        $callback($this);
        array_pop($this->groupMiddlewareStack);
        $this->prefix = $previousPrefix;
    }

    /**
     * Add middleware
     *
     * @param array $middleware
     * @param callable $callback
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function withMiddleware(array $middleware, callable $callback): void
    {
        $this->groupMiddlewareStack[] = $middleware;
        $callback($this);
        array_pop($this->groupMiddlewareStack);
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
        foreach ($this->groupMiddlewareStack as $group) {
            $middleware = array_merge($middleware, $group);
        }

        return array_values(array_unique($middleware));
    }

    /**
     * @param array $middleware
     *
     * @return $this
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function middleware(array $middleware): self
    {
        $this->routeMiddlewareConfig['_extra'] = $middleware;

        return $this;
    }

    /**
     * Add middleware to route
     *
     * @param string $name
     * @param array $middleware
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function addMiddlewareToRoute(string $name, array $middleware): void
    {
        $this->routeMiddlewareMeta[$name]['extra'] = array_merge(
            $this->routeMiddlewareMeta[$name]['extra'],
            $middleware
        );
    }

    /**
     * Remove inherited middleware
     *
     * @param string $name
     * @param array $middleware
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function withoutMiddleware(string $name, array $middleware): void
    {
        $this->routeMiddlewareMeta[$name]['except'] = array_merge(
            $this->routeMiddlewareMeta[$name]['except'],
            $middleware
        );
    }

    /**
     * Set the middleware priority order
     *
     * @param string $name
     * @param array $priority
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function setMiddlewarePriority(string $name, array $priority): void
    {
        $this->routeMiddlewareMeta[$name]['priority'] = $priority;
    }

    /**
     * Build the route middleware
     *
     * @param string $routeName
     *
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function buildRouteMiddleware(string $routeName): array
    {
        $route = $this->routes->get($routeName);
        $base = $route->getDefault('_middleware') ?? [];

        $meta = $this->routeMiddlewareMeta[$routeName] ?? [
            'extra' => [],
            'except' => [],
            'priority' => [],
        ];

        // Remove excluded middleware
        $middleware = array_values(array_filter($base, fn ($m) => !in_array($m, $meta['except'], true)));

        // Add route-specific middleware
        $middleware = array_merge($middleware, $meta['extra']);

        // Apply priority ordering
        if (!empty($meta['priority'])) {
            $middleware = $this->sortByPriority($middleware, $meta['priority']);
        }

        $middleware = array_values(array_unique($middleware));

        return array_map(
            fn (string $middleware) => $this->parseMiddleware($middleware),
            $middleware
        );
    }

    /**
     * Sort middleware by priority
     *
     * @param array $middleware
     * @param array $priority
     *
     * @return array
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function sortByPriority(array $middleware, array $priority): array
    {
        $sorted = [];
        $added = [];

        foreach ($priority as $name) {
            if (in_array($name, $middleware, true)) {
                $sorted[] = $name;
                $added[$name] = true;
            }
        }

        foreach ($middleware as $name) {
            if (!isset($added[$name])) {
                $sorted[] = $name;
            }
        }

        return $sorted;
    }

    /**
     * Resolve middleware alias
     *
     * @param string $alias
     *
     * @return string
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function resolveMiddlewareAlias(string $alias): string
    {
        return $this->middlewareMap[$alias] ?? $alias;
    }

    /**
     * Parse the middleware
     *
     * @param string $middleware
     *
     * @return MiddlewareDefinition
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function parseMiddleware(string $middleware): MiddlewareDefinition
    {
        $parameters = [];

        if (str_contains($middleware, ':')) {
            [$middleware, $parameterString] = explode(':', $middleware, 2);
            $parameters = array_map('trim', explode(',', $parameterString));
        }

        $class = $this->resolveMiddlewareAlias($middleware);

        return new MiddlewareDefinition(
            class: $class,
            parameters: $parameters,
        );
    }
}
