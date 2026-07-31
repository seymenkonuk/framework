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

use Predis\Client as Redis;

use Seymenkonuk\Framework\Cache\ICache;
use Seymenkonuk\Framework\Cache\RedisCache;

use Seymenkonuk\Validator\Localization\FileLoader;
use Seymenkonuk\Validator\Localization\Translator;
use Seymenkonuk\Validator\Validator\Validator;


final class Application
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    protected Session $session;
    protected ICache $cache;
    protected Database $database;
    protected Request $request;
    protected Response $response;
    protected Container $container;
    protected Router $router;
    protected Validator $validator;

    protected ?Closure $dbConnectCallback = null;
    protected ?string $routeConfig = null;
    /** @var array<string, Closure(): Response> $exceptionCallbacks */
    protected array $exceptionCallbacks = [];

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    private function __construct(protected string $basePath)
    {
        $this->session = new Session();
        $this->database = new Database();
        $this->request = new Request();
        $this->response = new Response(new TemplateEngine($basePath));
        $this->container = new Container();
        $this->router = new Router($this->container);

        $this->validator = new Validator(new Translator(
            new FileLoader(),
            "tr",
        ));

        $redisConfig = [
            "host" => getenv("REDIS_HOST") ?: "127.0.0.1",
            "port" => getenv("REDIS_PORT") ?: "6379",
        ];

        $redisPassword = getenv("REDIS_PASSWORD") ?: "";

        if ($redisPassword !== "") {
            $redisConfig["password"] = $redisPassword;
        }

        $this->cache = new RedisCache(new Redis($redisConfig));

        $this->container->bind(ICache::class, RedisCache::class);
        $this->container->instance(ICache::class, $this->cache);
        $this->container->instance(Session::class, $this->session);
        $this->container->instance(Request::class, $this->request);
        $this->container->instance(Response::class, $this->response);
        $this->container->instance(Database::class, $this->database);
        $this->container->instance(Validator::class, $this->validator);
    }

    // --------------------------------------------------------------------------
    // CONFIGURATION
    // --------------------------------------------------------------------------

    public static function configure(string $basePath): self
    {
        return new self($basePath);
    }

    // --------------------------------------------------------------------------
    // ROUTE CONFIG CALLBACK
    // --------------------------------------------------------------------------

    public function withRouting(string $routeConfig): self
    {
        $this->routeConfig = $routeConfig;
        return $this;
    }

    // --------------------------------------------------------------------------
    // GLOBAL EXCEPTION HANDLER
    // --------------------------------------------------------------------------

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
    // DATABASE CONFIG CALLBACK
    // --------------------------------------------------------------------------

    public function withDbConfig(
        string $host,
        string $port,
        string $dbname,
        string $charset,
        string $username,
        string $password,
    ): self {
        $this->dbConnectCallback
            = fn() => $this->database->connect($host, $port, $dbname, $charset, $username, $password);
        return $this;
    }

    // --------------------------------------------------------------------------
    // RUN
    // --------------------------------------------------------------------------

    public function run(): void
    {
        /** @var ?Response $response  */
        $response = null;

        try {
            ob_start();

            // Veri Tabanına Bağlan
            if ($this->dbConnectCallback !== null) {
                $this->container->call($this->dbConnectCallback);
            }

            // Route Yapılandırmasını Yap
            if ($this->routeConfig !== null && method_exists($this->routeConfig, "register")) {
                $this->container->call([$this->routeConfig, "register"], ["router" => $this->router]);
            }
            // Controller'ı Çağır
            $response = $this->router->dispatch($this->request);
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

    private function handleException(Throwable $e): Response
    {
        foreach ($this->exceptionCallbacks as $exceptionClass => $callback) {
            if ($e instanceof $exceptionClass) {
                /** @var Response $response */
                $response = $this->container->call($callback, ["exception" => $e]);
                return $response;
            }
        }

        throw $e;
    }
}
