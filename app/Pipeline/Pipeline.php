<?php
/*
 * Project:     beacon
 * File:        Pipeline.php
 * Date:        2026-06-29
 * Author:      Steffen Haase <shworx.development@gmail.com
 * Copyright:   2026 SHWorX (Steffen Haase)
 */

namespace App\Pipeline;

use App\Container\Container;
use App\Http\Request;
use App\Routing\MiddlewareDefinition;

class Pipeline
{
    private mixed $passable;

    /** @var array<MiddlewareDefinition> */
    private array $pipes = [];

    /**
     * @param Container $container
     */
    public function __construct(
        private readonly Container $container,
    ) {}

    /**
     * @param mixed $passable
     *
     * @return $this
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function send(mixed $passable): self
    {
        $clone = clone $this;
        $clone->passable = $passable;
        return $clone;
    }

    /**
     * @param array<int, MiddlewareDefinition> $pipes
     *
     * @return $this
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function through(array $pipes): self
    {
        $clone = clone $this;
        $clone->pipes = $pipes;
        return $clone;
    }

    /**
     * @param callable $destination
     *
     * @return mixed
     * @author SteffenHaase <shworx.development@gmail.com>
     */
    public function then(callable $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            function (callable $next, MiddlewareDefinition $pipe): callable {
                return function (Request $request) use ($next, $pipe) {
                    $middleware = $this->container->make($pipe->class);

                    return $middleware->handle($request, $next, ...$pipe->parameters);
                };
            },
            $destination
        );

        return $pipeline($this->passable);
    }
}
