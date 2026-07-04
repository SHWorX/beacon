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
use App\Pipeline\Pipeline;
use ReflectionException;
use RuntimeException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

readonly class RouteDispatcher
{
    public function __construct(
        private Router $router,
        private Container $container,
        private Resolver $resolver,
        private Request $request,
        private Pipeline $pipeline,
    ) { }

    /**
     * Dispatcher
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function dispatch(): mixed
    {
        $context = $this->match();
        $context = $this->resolve($context);

        return $this->execute($context);
    }

    /**
     * Execute handler
     *
     * @throws ReflectionException
     * @throws RuntimeException
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
     * Execute controller
     *
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

    /**
     * Matcher
     *
     * @return array
     * @throws RuntimeException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function match(): array
    {
        $context = new RequestContext();
        $context->setMethod($this->request->method());

        $matcher = new UrlMatcher($this->router->getRoutes(), $context);
        $parameters = $matcher->match($this->request->uri());

        $routeName = $parameters['_route'] ?? null;
        if (!$routeName) {
            throw new RuntimeException('Route name missing.');
        }

        $_SERVER['_route'] = $parameters['_route'];

        return [
            'routeName' => $routeName,
            'handler' => $parameters['_handler'],
            'parameters' => $parameters,
        ];
    }

    /**
     * Resolver
     *
     * @param array $data
     *
     * @return RouteExecutionContext
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function resolve(array $data): RouteExecutionContext
    {
        $routeName = $data['routeName'];
        $middleware = $this->router->buildRouteMiddleware($routeName);
        $parameters = $data['parameters'];

        unset(
            $parameters['_handler'],
            $parameters['_route']
        );

        return new RouteExecutionContext(
            request: $this->request,
            routeName: $routeName,
            handler: $data['handler'],
            parameters: $parameters,
            middleware: $middleware,
        );
    }

    /**
     * Pipeline execution
     *
     * @param RouteExecutionContext $context
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    private function execute(RouteExecutionContext $context): mixed
    {
        return $this->pipeline
            ->send($this->request)
            ->through($context->middleware)
            ->then(fn () => $this->executeHandler($context->handler, $context->parameters));
    }
}