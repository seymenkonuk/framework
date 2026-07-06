<?php
// ============================================================================
// File:    Router.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use InvalidArgumentException;

use ReflectionClass;
use ReflectionMethod;
use ReflectionAttribute;

use Seymenkonuk\Framework\Exception\AuthorizationException;
use Seymenkonuk\Framework\Exception\RouteConflictException;
use Seymenkonuk\Framework\Exception\RouteNotFoundException;
use Seymenkonuk\Framework\Exception\ValidationException;

use Seymenkonuk\Framework\Attribute\Name;
use Seymenkonuk\Framework\Attribute\Schema;
use Seymenkonuk\Framework\Attribute\Prefix;
use Seymenkonuk\Framework\Attribute\Middleware;
use Seymenkonuk\Framework\Attribute\Route\Route as RouteAttribute;
use Seymenkonuk\Framework\Attribute\Where\Where;

use Seymenkonuk\Validator\Validator\ValidationResult;


final class Router
{
    /**
     * @var array{
     *      GET?: array<string, Route>,
     *      QUERY?: array<string, Route>,
     *      POST?: array<string, Route>,
     *      PUT?: array<string, Route>,
     *      PATCH?: array<string, Route>,
     *      DELETE?: array<string, Route>
     * }
     */
    private array $routes = [];

    /** 
     * @var array<array{
     *      prefix?: string,
     *      middleware?: array<string>
     * }>  
     */
    private array $groupStack = [];

    /** @var array<string> */
    private array $middlewares = [];

    // ------------------------------------------------------------------
    // DEPENDENCIES
    // ------------------------------------------------------------------

    public function __construct(
        private Container $container
    ) {}

    // ------------------------------------------------------------------
    // ROUTES
    // ------------------------------------------------------------------

    /** @param array{0: string, 1: string} $handler */
    public function get(string $uri, array $handler): Route
    {
        return $this->addRoute("GET", $uri, $handler);
    }

    /** @param array{0: string, 1: string} $handler */
    public function query(string $uri, array $handler): Route
    {
        return $this->addRoute("QUERY", $uri, $handler);
    }

    /** @param array{0: string, 1: string} $handler */
    public function post(string $uri, array $handler): Route
    {
        return $this->addRoute("POST", $uri, $handler);
    }

    /** @param array{0: string, 1: string} $handler */
    public function put(string $uri, array $handler): Route
    {
        return $this->addRoute("PUT", $uri, $handler);
    }

    /** @param array{0: string, 1: string} $handler */
    public function patch(string $uri, array $handler): Route
    {
        return $this->addRoute("PATCH", $uri, $handler);
    }

    /** @param array{0: string, 1: string} $handler */
    public function delete(string $uri, array $handler): Route
    {
        return $this->addRoute("DELETE", $uri, $handler);
    }

    /** @param array{0: string, 1: string} $handler */
    public function any(string $uri, array $handler): Route
    {
        return $this->match(["GET", "QUERY", "POST", "PUT", "PATCH", "DELETE"], $uri, $handler);
    }

    /**
     * @param array<string> $methods
     * @param array{0: string, 1: string} $handler
     */
    public function match(array $methods, string $uri, array $handler): Route
    {
        if (count($methods) == 0) {
            throw new InvalidArgumentException(
                "At least one HTTP method is required."
            );
        }

        return $this->addRoute(array_map(fn($method) => strtoupper($method), $methods), $uri, $handler);
    }

    // ------------------------------------------------------------------
    // GROUP
    // ------------------------------------------------------------------

    /** 
     * @param array{
     *      prefix?: string,
     *      middleware?: array<string>
     * } $attributes 
     */
    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    // ------------------------------------------------------------------
    // MIDDLEWARE
    // ------------------------------------------------------------------

    /** @param array<string>|string $middleware */
    public function middleware(array|string $middleware): void
    {
        $this->middlewares = array_merge($this->middlewares, (array)$middleware);
    }

    // ------------------------------------------------------------------
    // CORE ADD ROUTE
    // ------------------------------------------------------------------

    /**
     * @param array<string>|string $methods
     * @param array{0: string, 1: string} $handler
     */
    private function addRoute(array|string $methods, string $uri, array $handler): Route
    {
        // Route Oluştur
        $route = new Route(
            methods: (array)$methods,
            uri: $this->buildUri($uri),
            handler: $handler,
            middleware: $this->buildMiddleware(),
        );
        // Route'lara Ekle
        foreach ((array)$methods as $method) {
            // Zaten Varsa Hata Ver
            if (array_key_exists($method, $this->routes) && array_key_exists($uri, $this->routes[$method])) {
                throw new RouteConflictException($method, $uri);
            }
            // Ekle
            $this->routes[$method][$uri] = $route;
        }

        return $route;
    }

    // ------------------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------------------

    private function buildUri(string $uri): string
    {
        $prefix = "";

        foreach ($this->groupStack as $group) {
            $prefix .= "/";
            $prefix .= trim($group["prefix"] ?? "", "/");
            $prefix = rtrim($prefix, "/");
        }

        return "/" . trim($prefix . "/" . trim($uri, "/"), "/");
    }

    /** @return array<string> */
    private function buildMiddleware(): array
    {
        $middleware = array_merge([], $this->middlewares);

        foreach ($this->groupStack as $group) {
            $middleware = array_merge(
                $middleware,
                $group["middleware"] ?? []
            );
        }

        return $middleware;
    }

    // ------------------------------------------------------------------
    // REGISTER CONTROLLER (WITH ATTRIBUTES)
    // ------------------------------------------------------------------

    public function registerController(string $controller): void
    {
        // @phpstan-ignore-next-line
        $reflection = new ReflectionClass($controller);
        // Prefix'i Öğren
        /** @var Prefix|null $prefixAttribute */
        $prefixAttribute = $this->getAttribute($reflection, Prefix::class);
        $prefix = $prefixAttribute !== null ? $prefixAttribute->uri : "";
        // Class Middleware'lerini Öğren
        $controllerMiddlewares = array_map(
            function ($object) {
                /** @var Middleware $object */
                return $object->middleware;
            },
            $this->getAttributes($reflection, Middleware::class),
        );
        // Class'ın Metotlarını Öğren
        foreach ($reflection->getMethods() as $method) {
            // Route Attribute
            /** @var RouteAttribute|null $route */
            $route = $this->getAttribute($method, RouteAttribute::class, ReflectionAttribute::IS_INSTANCEOF);
            // Route'u Yoksa Action Metot Değildir
            if ($route === null) {
                continue;
            }
            // Route'u Öğren
            $methods = (array)$route->methods;
            $uri = "/" . trim(trim($prefix, "/") . "/" . trim($route->uri, "/"), "/");
            // Name Attribute
            /** @var Name|null $nameAttribute */
            $nameAttribute = $this->getAttribute($method, Name::class);
            $name = $nameAttribute !== null ? $nameAttribute->name : null;
            // Schema Attribute
            /** @var Schema|null $schemaAttribute */
            $schemaAttribute = $this->getAttribute($method, Schema::class);
            $schema = $schemaAttribute !== null ? $schemaAttribute->schema : null;
            // Middleware Attributes
            $middlewares = array_map(
                function ($object) {
                    /** @var Middleware $object */
                    return $object->middleware;
                },
                $this->getAttributes($method, Middleware::class),
            );
            // Where Attributes
            $where = array_column(array_map(
                function ($object) {
                    /** @var Where $object */
                    return [
                        "key" => $object->key,
                        "value" => $object->pattern,
                    ];
                },
                $this->getAttributes($method, Where::class, ReflectionAttribute::IS_INSTANCEOF)
            ), "value", "key");

            // Route Olarak Kaydet
            $this->match($methods, $uri, [$controller, $method->getName()])
                ->name($name)
                ->schema($schema)
                ->whereMany($where)
                ->middleware(array_merge($controllerMiddlewares, $middlewares));
        }
    }

    // ------------------------------------------------------------------
    // REGISTER CONTROLLER INTERNAL HELPERS
    // ------------------------------------------------------------------

    /** @param ReflectionClass<object>|ReflectionMethod $reflection */
    private function getAttribute(ReflectionClass|ReflectionMethod $reflection, string $attributeName, int $flags = 0): ?object
    {
        $attributes = $reflection->getAttributes($attributeName, $flags);
        return isset($attributes[0])
            ? $attributes[0]->newInstance()
            : null;
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod $reflection 
     * @return array<object> 
     */
    private function getAttributes(ReflectionClass|ReflectionMethod $reflection, string $attributeName, int $flags = 0): array
    {
        return array_map(
            fn($attribute) => $attribute->newInstance(),
            $reflection->getAttributes($attributeName, $flags)
        );
    }

    // ------------------------------------------------------------------
    // DISPATCH
    // ------------------------------------------------------------------

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes[$method] ?? [] as $route) {
            $params = [];

            if (!$this->matchUri($route, $uri, $params)) {
                continue;
            }

            $request->setRoutes($params);
            return $this->run($route, $request, $params);
        }

        throw new RouteNotFoundException($method, $uri);
    }

    // ------------------------------------------------------------------
    // MATCH
    // ------------------------------------------------------------------

    /** @param array<string, mixed> $params */
    private function matchUri(Route $route, string $uri, array &$params): bool
    {
        // Route Uri'yi Regex Desenine Dönüştür
        $pattern = preg_replace_callback(
            "/\{(\w+?)\}/",
            function ($m) use ($route) {
                $key = $m[1];
                $regex = $route->where[$key] ?? "\w+";
                return "(?P<" . $key . ">" . $regex . ")";
            },
            $route->uri
        );
        $pattern = "#^" . $pattern . "$#";

        // Request Uri Desene Uymuyorsa False Döndür
        if (!preg_match($pattern, $uri, $matches)) {
            return false;
        }

        // Uri'den Gelen Parametreleri Diziye Kaydet
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return true;
    }

    // ------------------------------------------------------------------
    // EXECUTION (placeholder)
    // ------------------------------------------------------------------

    /** @param array<string, mixed> $params */
    private function run(Route $route, Request $request, array $params): Response
    {
        $currentFunction = fn(Request $request) => $this->container->call($route->handler, array_merge($params, ["request" => $request]));

        // Middleware'leri Ekle
        for ($i = count($route->middleware) - 1; $i >= 0; $i--) {
            $currentFunction = fn(Request $request)
            => $this->container->call([$route->middleware[$i], "handle"], ["next" => $currentFunction, "request" => $request]);
        }

        if ($route->schema !== null) {
            // Validation Kontrolü
            if (method_exists($route->schema, "validate")) {
                /** @var ValidationResult $result */
                $result = $this->container->call([$route->schema, "validate"], [
                    "data" => $request->all(),
                ]);
                // Validation Error
                if ($result->failed()) {
                    throw new ValidationException($result->errors());
                }
            }
            // Authorization Kontrolü
            if (method_exists($route->schema, "authorize")) {
                /** @var array{
                 *      title: string,
                 *      description: string
                 * }|null $result */
                $result = $this->container->call([$route->schema, "authorize"]);
                // Authorization Error
                if ($result !== null) {
                    throw new AuthorizationException($result["title"], $result["description"]);
                }
            }
        }

        // @phpstan-ignore-next-line
        return $currentFunction($request);
    }
}
