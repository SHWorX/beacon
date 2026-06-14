<?php
/*
 * Project:     Beacon
 * File:        Resolver.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Container;

use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;
use RuntimeException;

readonly class Resolver
{
    public function __construct(
        private Container $container
    ) { }

    /**
     * @param callable $callable
     * @param array $parameters
     *
     * @return array
     * @throws ReflectionException
     * @throws RuntimeException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function resolve(callable $callable, array $parameters = []): array {
        $reflection = new ReflectionFunction($callable(...));

        $resolved = [];

        foreach ($reflection->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type !== null && !$type->isBuiltin()) {
                $resolved[] = $this->resolveClassParameter(
                    $type->getName(),
                    $parameter,
                    $parameters
                );
                continue;
            }

            $name = $parameter->getName();

            if (array_key_exists($name, $parameters)) {
                $resolved[] = $parameters[$name];
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $resolved[] = $parameter->getDefaultValue();
                continue;
            }

            throw new RuntimeException(sprintf(
                'Unable to resolve parameter [%s] for callable.',
                $name
            ));
        }

        return $resolved;
    }

    /**
     * @param string $class
     * @param ReflectionParameter $parameter
     * @param array $routeParameters
     *
     * @return mixed
     * @throws ReflectionException
     */
    private function resolveClassParameter(
        string $class,
        ReflectionParameter $parameter,
        array $routeParameters
    ): mixed
    {
        return $this->container->make($class);
    }
}