<?php
/*
 * Project:     Beacon
 * File:        Container.php
 * Date:        2026-06-01
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

class Container
{
    /** @var array <string, Closure> */
    private array $bindings = [];

    /** @var array array<string, array{factory: Closure, instance: object|null}> */
    private array $singletons = [];

    /**
     * @param string $abstract
     * @param Closure $factory
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function bind(string $abstract, Closure $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    /**
     * @param string $abstract
     * @param Closure $factory
     *
     * @return void
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function singleton(string $abstract, Closure $factory): void
    {
        $this->singletons[$abstract] = [
            'factory' => $factory,
            'instance' => null,
        ];
    }

    /**
     * @param string $abstract
     *
     * @return bool
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->singletons[$abstract]);
    }

    /**
     * @param string $abstract
     *
     * @return object
     * @throws ReflectionException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function make(string $abstract): object
    {
        if (isset($this->singletons[$abstract])) {
            if ($this->singletons[$abstract]['instance'] === null) {
                $this->singletons[$abstract]['instance'] = ($this->singletons[$abstract]['factory'])($this);
            }

            return $this->singletons[$abstract]['instance'];
        }

        if (isset($this->bindings[$abstract])) {
            return ($this->bindings[$abstract])($this);
        }

        return $this->build($abstract);
    }

    /**
     * @param string $class
     *
     * @return object
     * @throws ReflectionException
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function build(string $class): object
    {
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('Class [%s] does not exist.', $class));
        }

        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException(sprintf('Class [%s] is not instantiable.', $class));
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class;
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if ($type === null || $type->isBuiltin()) {
                throw new RuntimeException(sprintf(
                    'Unable to resolve constructor parameter [%s] in class [%s].',
                    $param->getName(),
                    $class
                ));
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}