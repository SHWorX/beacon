<?php
/*
 * Project:     Beacon
 * File:        RouteDispatcher.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Routing;

use App\Container\Container;
use App\Container\Resolver;
use App\Http\Request;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

readonly class RouteDispatcher
{
    public function __construct(
        private Router    $router,
        private Container $container,
        private Resolver  $resolver,
    ) { }

    /**
     * @throws ReflectionException
     */
    public function dispatch(Request $request): mixed
    {
        $context = new RequestContext();
        $context->setMethod($request->method());

        $matcher = new UrlMatcher($this->router->getRoutes(), $context);
        $parameters = $matcher->match($request->uri());
        $_SERVER['_route'] = $parameters['_route'];
        $handler = $parameters['_handler'];
        $middleware = $parameters['_middleware'] ?? [];

        unset(
            $parameters['_handler'],
            $parameters['_route'],
            $parameters['_middleware'],
        );

        $pipeline = array_reduce(
            array_reverse($middleware),
            fn (callable $next, string $middlewareClass) =>
            fn (Request $request) =>
            $this->container
                ->make($middlewareClass)
                ->handle($request, $next),
            fn (Request $request) =>
            $this->executeHandler($handler, $parameters)
        );

        return $pipeline($request);
    }

    /**
     * @throws ReflectionException
     */
    private function executeHandler(mixed $handler, array $parameters): mixed
    {
        if (is_callable($handler)) {
            return $this->invoke($handler, $parameters);
        }

        if (is_array($handler)) {
            return $this->executeController($handler, $parameters);
        }

        throw new RuntimeException('Invalid route handler.');
    }

    /**
     * @param array $handler
     * @param array $parameters
     *
     * @return mixed
     * @throws ReflectionException
     */
    private function executeController(array $handler, array $parameters): mixed {
        [$controllerClass, $method] = $handler;
        $controller = $this->container->make($controllerClass);

        return $this->invoke([$controller, $method], $parameters);
    }

    /**
     * @throws ReflectionException
     */
    private function invoke(callable $callable, array $parameters): mixed
    {
        $arguments = $this->resolver->resolve($callable, $parameters);

        return $callable(...$arguments);
    }
}