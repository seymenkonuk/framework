<?php
// ============================================================================
// File:    Application.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use Closure;
use Throwable;

use ReflectionFunction;
use ReflectionNamedType;
use ReflectionUnionType;
use ReflectionIntersectionType;

use Seymenkonuk\Framework\Http\Request\IRequest;
use Seymenkonuk\Framework\Http\Response\IResponse;
use Seymenkonuk\Framework\Routing\RouteConfig;
use Seymenkonuk\Framework\Routing\Router;


final class Application
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    protected Container $container;
    protected Router $router;

    /** @var class-string<RouteConfig> */
    protected ?string $routeConfig = null;

    /** @var array<string, Closure(mixed...): IResponse> */
    protected array $exceptionCallbacks = [];

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router($this->container);
    }

    // --------------------------------------------------------------------------
    // ROUTE CONFIGURATION
    // --------------------------------------------------------------------------

    /** @param class-string<RouteConfig> $routeConfig */
    public function withRouting(string $routeConfig): self
    {
        $this->routeConfig = $routeConfig;
        return $this;
    }

    // --------------------------------------------------------------------------
    // CONTAINER CONFIGURATION
    // --------------------------------------------------------------------------

    /** @param array<class-string, class-string> $bindings */
    public function withBindings(array $bindings): self
    {
        foreach ($bindings as $abstract => $concrete) {
            $this->container->bind($abstract, $concrete);
        }
        return $this;
    }

    /** @param array<class-string, object> $instances */
    public function withInstances(array $instances): self
    {
        foreach ($instances as $class => $instance) {
            $this->container->instance($class, $instance);
        }

        return $this;
    }

    /** @param array<class-string, Closure(mixed...): object> $singletons */
    public function withSingletons(array $singletons): self
    {
        foreach ($singletons as $class => $factory) {
            $this->container->singleton($class, $factory);
        }
        return $this;
    }

    // --------------------------------------------------------------------------
    // GLOBAL EXCEPTION HANDLER
    // --------------------------------------------------------------------------

    /** @param Closure(mixed...): IResponse $callback */
    public function withException(Closure $callback): self
    {
        $reflection = new ReflectionFunction($callback);

        foreach ($reflection->getParameters() as $parameter) {
            if ($parameter->getName() !== "exception") {
                continue;
            }

            $type = $parameter->getType();

            $types = match (true) {
                $type instanceof ReflectionUnionType => $type->getTypes(),
                $type instanceof ReflectionNamedType => [$type],
                default => [],
            };

            foreach ($types as $reflectionType) {
                if ($reflectionType instanceof ReflectionIntersectionType) {
                    continue;
                }
                $this->exceptionCallbacks[$reflectionType->getName()] = $callback;
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------
    // RUN
    // --------------------------------------------------------------------------

    public function run(): void
    {
        $request = $this->container->make(IRequest::class);
        $response = $this->container->make(IResponse::class);

        try {
            // Çıktıları Buffer'da Topla
            ob_start();

            // Route Yapılandırmasını Yap
            if ($this->routeConfig !== null) {
                $routeConfig = new $this->routeConfig();
                $routeConfig->register($this->router);
            }

            // Controller'ı Çağır
            $response = $this->router->dispatch($request);
        } catch (Throwable $e) {
            $response = $this->handleException($e);
        }

        // Bufferdakileri Çöpe At
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Response'u Göster
        $response->send();
    }

    private function handleException(Throwable $e): IResponse
    {
        foreach ($this->exceptionCallbacks as $exceptionClass => $callback) {
            if ($e instanceof $exceptionClass) {
                $response = $this->container->call($callback, ["exception" => $e]);
                return $response;
            }
        }

        throw $e;
    }
}
