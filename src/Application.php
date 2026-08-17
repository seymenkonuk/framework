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

use ReflectionNamedType;
use ReflectionUnionType;
use ReflectionIntersectionType;

use Seymenkonuk\Framework\Http\Request\IRequest;
use Seymenkonuk\Framework\Http\Response\IResponse;
use Seymenkonuk\Framework\Http\Response\IResponseState;
use Seymenkonuk\Framework\Reflection\Reflect;
use Seymenkonuk\Framework\Routing\RouteConfig;
use Seymenkonuk\Framework\Routing\Router;


final class Application
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    /**
     * Uygulamanın bağımlılıklarını çözümlemek için kullanılan container.
     * 
     * @var Container
     */
    protected Container $container;

    /**
     * Uygulamanın route'larını yöneten router.
     * 
     * @var Router
     */
    protected Router $router;

    /**
     * Uygulama tarafından kullanılacak route yapılandırma sınıfını saklar.
     *
     * @var ?class-string<RouteConfig>
     */
    protected ?string $routeConfig = null;

    /**
     * Exception türleri için tanımlanan handler'ları saklar.
     *
     * @var array<string, Closure(mixed...): IResponse>
     */
    protected array $exceptionCallbacks = [];

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    /**
     * Yeni bir application örneği oluşturur.
     *
     * @return void
     */
    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router($this->container);
    }

    // --------------------------------------------------------------------------
    // ROUTE CONFIGURATION
    // --------------------------------------------------------------------------

    /**
     * Uygulamanın router'ını döndürür.
     *
     * @return Router uygulamanın router'ı.
     */
    public function router(): Router
    {
        return $this->router;
    }

    /**
     * Uygulama tarafından kullanılacak route yapılandırmasını tanımlar.
     *
     * @param class-string<RouteConfig> $routeConfig kullanılacak route yapılandırma sınıfı.
     *
     * @return self
     */
    public function withRouting(string $routeConfig): self
    {
        $this->routeConfig = $routeConfig;
        return $this;
    }

    // --------------------------------------------------------------------------
    // CONTAINER CONFIGURATION
    // --------------------------------------------------------------------------

    /**
     * Container'a birden fazla sınıf bağlantısı kaydeder.
     *
     * @param array<class-string, class-string> $bindings sınıf ve bağlantılı sınıf eşleşmeleri.
     *
     * @return self
     */
    public function withBindings(array $bindings): self
    {
        foreach ($bindings as $abstract => $concrete) {
            $this->container->bind($abstract, $concrete);
        }
        return $this;
    }

    /**
     * Container'a birden fazla nesne örneği kaydeder.
     *
     * @param array<class-string, object> $instances sınıf ve nesne örneği eşleşmeleri.
     *
     * @return self
     */
    public function withInstances(array $instances): self
    {
        foreach ($instances as $class => $instance) {
            $this->container->instance($class, $instance);
        }

        return $this;
    }

    /**
     * Container'a birden fazla singleton factory'si kaydeder.
     *
     * @param array<class-string, Closure(mixed...): object> $singletons sınıf ve factory eşleşmeleri.
     *
     * @return self
     */
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

    /**
     * Global exception handler kaydeder.
     *
     * Callback'in `exception` adında bir parametresi bulunmalıdır.
     * Bu parametrenin türüyle eşleşen exception'lar callback tarafından yakalanır.
     *
     * @param Closure(mixed...): IResponse $callback exception'ları işleyecek callback.
     *
     * @return self
     */
    public function withException(Closure $callback): self
    {
        $reflection = Reflect::closure($callback);

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

    /**
     * Uygulamayı çalıştırır.
     *
     * Gelen isteği işler ve oluşan response'u gönderir.
     *
     * @return IResponseState gönderilen response'un state'i.
     */
    public function run(): IResponseState
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
            // Exception Handler'ı Çağır
            $response = $this->handleException($e);
        }

        // Bufferdakileri Çöpe At
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Response'u Gönder
        return  $response->send();
    }

    // --------------------------------------------------------------------------
    // INTERNAL
    // --------------------------------------------------------------------------

    /**
     * Verilen exception için kayıtlı handler'ı çalıştırır.
     *
     * Eşleşen bir handler bulunamazsa exception yeniden fırlatılır.
     *
     * @param Throwable $e işlenecek exception.
     *
     * @return IResponse exception handler tarafından oluşturulan response.
     */
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
