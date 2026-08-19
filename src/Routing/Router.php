<?php
// ============================================================================
// File:    Router.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Routing;


use Closure;

use Seymenkonuk\Framework\Container;

use Seymenkonuk\Framework\Exception\RouteConflictException;
use Seymenkonuk\Framework\Exception\RouteNotFoundException;
use Seymenkonuk\Framework\Exception\ValidationException;

use Seymenkonuk\Framework\Attribute\Name;
use Seymenkonuk\Framework\Attribute\Schema;
use Seymenkonuk\Framework\Attribute\Prefix;
use Seymenkonuk\Framework\Attribute\Middleware as MiddlewareAttribute;
use Seymenkonuk\Framework\Attribute\Route\Route as RouteAttribute;
use Seymenkonuk\Framework\Attribute\Where\Where;
use Seymenkonuk\Framework\Http\Controller;
use Seymenkonuk\Framework\Http\Middleware;
use Seymenkonuk\Framework\Http\Request\IRequest;
use Seymenkonuk\Framework\Http\Response\IResponse;
use Seymenkonuk\Framework\Reflection\AttributeResolver;
use Seymenkonuk\Framework\Reflection\Reflect;


final class Router
{
    /**
     * Route'ları HTTP methodu ve URI'ye göre saklar.
     *
     * @var array<string, array<string, Route>>
     */
    private array $routes = [];

    /**
     * Aktif route gruplarının yapılandırmalarını saklar.
     *
     * @var array<array{
     *      prefix?: string,
     *      middleware?: array<class-string<Middleware>>
     * }>
     */
    private array $groupStack = [];

    /**
     * Tüm route'larda geçerli global middleware sınıflarını saklar.
     *
     * @var array<class-string<Middleware>>
     */
    private array $middlewares = [];

    // ------------------------------------------------------------------
    // DEPENDENCIES
    // ------------------------------------------------------------------

    /**
     * Yeni bir router oluşturur.
     *
     * @param Container $container bağımlılıkları çözümlemek için kullanılan container.
     *
     * @return void
     */
    public function __construct(
        private Container $container
    ) {}

    // ------------------------------------------------------------------
    // GROUP
    // ------------------------------------------------------------------

    /**
     * Route'ları ortak yapılandırma altında gruplar.
     *
     * Grup içerisinde tanımlanan route'lara belirtilen URI prefix'i ve
     * middleware'ler uygulanır.
     *
     * @param array{
     *      prefix?: string,
     *      middleware?: array<class-string<Middleware>>
     * } $attributes route grubunun yapılandırması.
     * @param Closure(Router): void $callback grup içerisinde route'ları tanımlayacak işlev.
     *
     * @return void
     */
    public function group(array $attributes, Closure $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    // ------------------------------------------------------------------
    // MIDDLEWARE
    // ------------------------------------------------------------------

    /**
     * Tüm route'larda kullanılacak global middleware'leri tanımlar.
     *
     * Daha önce tanımlanmış middleware'lerin üzerine yazmaz, yeni middleware'leri
     * mevcut listenin sonuna ekler.
     *
     * @param array<class-string<Middleware>>|class-string<Middleware> $middleware eklenecek middleware sınıfı veya sınıfları.
     *
     * @return void
     */
    public function middleware(array|string $middleware): void
    {
        $this->middlewares = array_merge($this->middlewares, (array)$middleware);
    }

    // ------------------------------------------------------------------
    // ROUTES
    // ------------------------------------------------------------------

    /**
     * HTTP GET route'u tanımlar.
     *
     * @param string $uri route'un eşleşeceği URI.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     *
     * @return Route oluşturulan route.
     */
    public function get(string $uri, array|Closure $handler): Route
    {
        return $this->addRoute("GET", $uri, $handler);
    }

    /**
     * HTTP QUERY route'u tanımlar.
     *
     * @param string $uri route'un eşleşeceği URI.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     *
     * @return Route oluşturulan route.
     */
    public function query(string $uri, array|Closure $handler): Route
    {
        return $this->addRoute("QUERY", $uri, $handler);
    }

    /**
     * HTTP POST route'u tanımlar.
     *
     * @param string $uri route'un eşleşeceği URI.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     *
     * @return Route oluşturulan route.
     */
    public function post(string $uri, array|Closure $handler): Route
    {
        return $this->addRoute("POST", $uri, $handler);
    }

    /**
     * HTTP PUT route'u tanımlar.
     *
     * @param string $uri route'un eşleşeceği URI.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     *
     * @return Route oluşturulan route.
     */
    public function put(string $uri, array|Closure $handler): Route
    {
        return $this->addRoute("PUT", $uri, $handler);
    }

    /**
     * HTTP PATCH route'u tanımlar.
     *
     * @param string $uri route'un eşleşeceği URI.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     *
     * @return Route oluşturulan route.
     */
    public function patch(string $uri, array|Closure $handler): Route
    {
        return $this->addRoute("PATCH", $uri, $handler);
    }

    /**
     * HTTP DELETE route'u tanımlar.
     *
     * @param string $uri route'un eşleşeceği URI.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     *
     * @return Route oluşturulan route.
     */
    public function delete(string $uri, array|Closure $handler): Route
    {
        return $this->addRoute("DELETE", $uri, $handler);
    }

    /**
     * Desteklenen tüm HTTP metotları için bir route tanımlar.
     *
     * @param string $uri route'un eşleşeceği URI.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     *
     * @return Route oluşturulan route.
     */
    public function any(string $uri, array|Closure $handler): Route
    {
        return $this->match(["GET", "QUERY", "POST", "PUT", "PATCH", "DELETE"], $uri, $handler);
    }

    /**
     * Belirtilen HTTP metotları için route tanımlar.
     *
     * @param array<string> $methods route tarafından kabul edilecek HTTP metotları.
     * @param string $uri route'un eşleşeceği URI.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     *
     * @return Route oluşturulan route.
     */
    public function match(array $methods, string $uri, array|Closure $handler): Route
    {
        return $this->addRoute($methods, $uri, $handler);
    }

    // ------------------------------------------------------------------
    // ATTRIBUTE ROUTING
    // ------------------------------------------------------------------

    /**
     * Controller sınıfındaki attribute'ları işler ve oluşturulan route'ları
     * router'a kaydeder.
     *
     * @param class-string<Controller> $controller route attribute'ları okunacak controller sınıfı.
     *
     * @return void
     */
    public function registerController(string $controller): void
    {
        $reflection = Reflect::class($controller);
        // Prefix'i Öğren
        $prefixAttribute = AttributeResolver::one($reflection, Prefix::class);
        $prefix = $prefixAttribute !== null ? $prefixAttribute->uri : "";
        // Class Middleware'lerini Öğren
        $controllerMiddlewares = array_map(function ($object) {
            return $object->middleware;
        }, AttributeResolver::all($reflection, MiddlewareAttribute::class));
        // Class'ın Metotlarını Öğren
        foreach ($reflection->getMethods() as $method) {
            // Route Attribute
            $route = AttributeResolver::one($method, RouteAttribute::class, AttributeResolver::IS_INSTANCEOF);
            // Route'u Yoksa Action Metot Değildir
            if ($route === null) {
                continue;
            }
            // Route'u Öğren
            $methods = (array)$route->methods;
            $uri = "/" . trim(trim($prefix, "/") . "/" . trim($route->uri, "/"), "/");
            // Name Attribute
            $nameAttribute = AttributeResolver::one($method, Name::class);
            $name = $nameAttribute !== null ? $nameAttribute->name : null;
            // Schema Attribute
            $schemaAttribute = AttributeResolver::one($method, Schema::class);
            $schema = $schemaAttribute !== null ? $schemaAttribute->schema : null;
            // Middleware Attributes
            $middlewares = array_map(function ($object) {
                return $object->middleware;
            }, AttributeResolver::all($method, MiddlewareAttribute::class));
            // Where Attributes
            $where = array_column(array_map(function ($object) {
                return [
                    "key" => $object->key,
                    "value" => $object->pattern,
                ];
            }, AttributeResolver::all($method, Where::class, AttributeResolver::IS_INSTANCEOF)), "value", "key");

            // Route Olarak Kaydet
            $newRoute = $this->match($methods, $uri, [$controller, $method->getName()])
                ->whereMany($where)
                ->middleware(array_merge($controllerMiddlewares, $middlewares));

            if ($schema !== null) {
                $newRoute->schema($schema);
            }

            if ($name !== null) {
                $newRoute->name($name);
            }
        }
    }

    // ------------------------------------------------------------------
    // REGISTRATION
    // ------------------------------------------------------------------

    /**
     * Belirtilen HTTP metotları için route tanımlar.
     *
     * Aktif route gruplarının prefix ve middleware yapılandırmalarını route'a
     * uygular ve oluşturulan route'u kayıtlı route'lar arasına ekler.
     *
     * Aynı HTTP metodu ve URI için daha önce route tanımlanmışsa
     * RouteConflictException fırlatılır.
     *
     * @param array<string>|string $methods route tarafından kabul edilecek HTTP metodu veya metotları.
     * @param string $uri route'un eşleşeceği URI.
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler route çalıştırıldığında çağrılacak handler.
     *
     * @throws RouteConflictException aynı HTTP metodu ve URI için daha önce route tanımlanmışsa.
     * 
     * @return Route oluşturulan route.
     */
    private function addRoute(array|string $methods, string $uri, array|Closure $handler): Route
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
            // Metot Dizisi Yoksa Oluştur
            if (!array_key_exists($method, $this->routes)) {
                $this->routes[$method] = [];
            }
            // URI için Önceden Route Tanımlandıysa Hata Ver
            if (array_key_exists($uri, $this->routes[$method])) {
                throw new RouteConflictException($method, $uri);
            }
            // Route'u Ekle
            $this->routes[$method][$uri] = $route;
        }
        // Route'u Döndür
        return $route;
    }

    /**
     * Route URI'sini aktif grupların prefix'leri ile oluşturur.
     *
     * @param string $uri route'un URI'si.
     *
     * @return string oluşturulan URI.
     */
    private function buildUri(string $uri): string
    {
        // Grup prefix'lerini normalize et ve boş değerleri kaldır.
        $parts = array_filter(array_map(
            fn(array $group): string => trim($group["prefix"] ?? "", "/"),
            $this->groupStack,
        ));
        // Route URI'sini son parçaya ekle.
        $parts[] = trim($uri, "/");

        return "/" . implode("/", $parts);
    }

    /**
     * Global middleware'lerin ve aktif grupların middleware'lerinin birleşimini döndürür.
     *
     * @return array<class-string<Middleware>> middleware listesi.
     */
    private function buildMiddleware(): array
    {
        // Grup Middleware'lerini Birleştir
        $groupMiddlewares = [];
        foreach ($this->groupStack as $group) {
            $groupMiddlewares = array_merge(
                $groupMiddlewares,
                $group["middleware"] ?? []
            );
        }

        // Global Middleware'ler ile Grup Middleware'lerini Birleştir
        return array_merge($this->middlewares, $groupMiddlewares);
    }

    // ------------------------------------------------------------------
    // DISPATCH
    // ------------------------------------------------------------------

    /**
     * İsteği eşleşen route'a yönlendirir.
     *
     * İstek için uygun bir route bulunursa route'un handler'ını çalıştırır ve
     * oluşturulan response'u döndürür.
     * 
     * İstek için uygun bir route bulunamazsa RouteNotFoundException fırlatılır.
     *
     * @param IRequest $request işlenecek HTTP isteği.
     *
     * @throws RouteNotFoundException istek için eşleşen bir route bulunamazsa.
     * 
     * @return IResponse route handler'ından oluşturulan response.
     */
    public function dispatch(IRequest $request): IResponse
    {
        $method = $request->method();
        $uri = $request->path();

        // İstekteki metot için tanımlanmış route'ları kontrol et.
        foreach ($this->routes[$method] ?? [] as $route) {
            $routeState = $route->state();
            $params = [];

            // URI route ile eşleşmiyorsa sonraki route'u kontrol et.
            if (!$this->matchUri($routeState, $uri, $params)) {
                continue;
            }

            // Route parametrelerini request'e ekle.
            $request->with([
                "params" => $params,
            ]);

            return $this->run($routeState, $request, $params);
        }

        throw new RouteNotFoundException($method, $uri);
    }

    /**
     * Route'un URI ile eşleşip eşleşmediğini kontrol eder.
     *
     * URI parametrelerini eşleşme sonucunda verilen parametre dizisine aktarır.
     *
     * @param RouteState $route kontrol edilecek route.
     * @param string $uri eşleştirilecek URI.
     * @param array<string, mixed> $params route parametrelerinin yazılacağı dizi.
     *
     * @return bool route URI ile eşleşiyorsa true, aksi halde false.
     */
    private function matchUri(RouteState $route, string $uri, array &$params): bool
    {
        // Route Uri'yi Regex Desenine Dönüştür
        $pattern = preg_replace_callback("/\{(\w+?)\}/", function ($m) use ($route) {
            $key = $m[1];
            $regex = $route->where($key) ?? "\w+";
            return "(?P<" . $key . ">" . $regex . ")";
        }, $route->uri());
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

    /**
     * Route'un middleware zincirini oluşturur ve zincirin ilk halkasını çalıştırır.
     *
     * @param RouteState $route çalıştırılacak route.
     * @param IRequest $request işlenecek HTTP isteği.
     * @param array<string, mixed> $params route parametreleri.
     *
     * @return IResponse middleware zinciri ve route handler'ı tarafından oluşturulan response.
     */
    private function run(RouteState $route, IRequest $request, array $params): IResponse
    {
        if ($route->schema() !== null) {
            $schema = $this->container->make($route->schema());
            $result = $schema->validate($request->all());

            if ($result->failed()) {
                throw new ValidationException($result->errors());
            }

            // Doğrulanmış verileri request'e ekle.
            $validated = $result->validated();
            $request->with($validated); // @phpstan-ignore argument.type
        }

        // Route handler'ını Closure'a dönüştür.
        $handler = $this->buildHandler($route->handler());

        // Route handler'ını middleware zincirinin sonuna yerleştir.
        $currentFunction = fn(IRequest $request, IResponse $response): IResponse
        => $this->container->call($handler, array_merge($params, [
            "request" => $request,
            "response" => $response,
        ]));

        // Middleware'leri ters sırayla zincire ekle.
        $middlewares = $route->allMiddleware();
        for ($i = count($middlewares) - 1; $i >= 0; $i--) {
            $middleware = $this->container->make($middlewares[$i]);
            $currentFunction = fn(IRequest $request, IResponse $response): IResponse
            => $middleware->handle($request, $response, $currentFunction);
        }

        // Middleware zincirini çalıştır.
        $response = $this->container->make(IResponse::class);
        return $currentFunction($request, $response);
    }

    /**
     * Handler'ı Closure'a dönüştürür.
     *
     * Controller handler'ları container üzerinden çözümlenir ve Closure'a dönüştürülür.
     * Closure handler'ları doğrudan döndürülür.
     *
     * @param array{class-string<Controller>, string}|Closure(mixed...): IResponse $handler dönüştürülecek handler.
     *
     * @return Closure(mixed...): IResponse oluşturulan handler.
     */
    private function buildHandler(array|Closure $handler): Closure
    {
        if ($handler instanceof Closure) {
            return $handler;
        }

        /** @var callable(mixed...): IResponse */
        $callable = [$this->container->make($handler[0]), $handler[1]];

        return Closure::fromCallable($callable);
    }
}
