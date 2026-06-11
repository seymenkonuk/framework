<?php
// ============================================================================
// File:    Container.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use Closure;

use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;

use RuntimeException;


final class Container
{
    /**
     * @var array<string, string>
     */
    private array $bindings = [];

    /**
     * @var array<string, object>
     */
    private array $instances = [];

    // --------------------------------------------------------------------------
    // BINDINGS
    // --------------------------------------------------------------------------

    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    // --------------------------------------------------------------------------
    // RESOLUTION
    // --------------------------------------------------------------------------

    public function make(string $abstract): object
    {
        // Daha önce instance olarak kayıtlıysa
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Binding varsa
        if (isset($this->bindings[$abstract])) {
            $abstract = $this->bindings[$abstract];
        }

        return $this->build($abstract);
    }

    // --------------------------------------------------------------------------
    // CALLABLE INJECTION
    // --------------------------------------------------------------------------

    /**
     * @param Closure|array{0: string, 1: string} $callable
     * @param array<string, mixed> $parameters
     */
    public function call(Closure|array $callable, array $parameters = []): mixed
    {
        // Method ise
        if (is_array($callable)) {
            /** @var array{0: string, 1: string} $callable */
            [$object, $method] = $callable;

            $object = $this->make($object);
            $reflection = new ReflectionMethod($object, $method);

            $dependencies = $this->resolveParameters(
                $reflection->getParameters(),
                $parameters
            );

            return $reflection->invokeArgs(
                $object,
                $dependencies
            );
        }

        // Function ise
        $reflection = new ReflectionFunction($callable);

        $dependencies = $this->resolveParameters(
            $reflection->getParameters(),
            $parameters
        );

        return $reflection->invokeArgs(
            $dependencies
        );
    }

    // --------------------------------------------------------------------------
    // INTERNAL
    // --------------------------------------------------------------------------

    private function build(string $class): object
    {
        // @phpstan-ignore-next-line
        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException(
                "Class [$class] is not instantiable."
            );
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $dependencies = $this->resolveParameters(
            $constructor->getParameters()
        );

        return $reflection->newInstanceArgs(
            $dependencies
        );
    }

    /**
     * @param array<ReflectionParameter> $parameters
     * @param array<string, mixed> $provided
     * @return array<mixed>
     */
    private function resolveParameters(array $parameters, array $provided = []): array
    {
        $resolved = [];

        foreach ($parameters as $parameter) {

            // Route parametreleri vb.
            if (array_key_exists($parameter->getName(), $provided)) {
                $resolved[] = $provided[$parameter->getName()];
                continue;
            }

            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $resolved[] = $this->make($type->getName());
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $resolved[] = $parameter->getDefaultValue();
                continue;
            }

            throw new RuntimeException("Unable to resolve parameter: " . $parameter->getName() . "!");
        }

        return $resolved;
    }
}
