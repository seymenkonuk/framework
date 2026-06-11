<?php
// ============================================================================
// File:    Application.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use Predis\Client as Redis;

use Seymenkonuk\Validator\Localization\FileLoader;
use Seymenkonuk\Validator\Localization\Translator;
use Seymenkonuk\Validator\Validator\Validator;


final class Application
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    protected Container $container;

    protected TemplateEngine $templateEngine;
    protected Response $response;
    protected Request $request;

    protected Redis $redis;
    protected Cache $cache;

    protected Database $database;

    protected Validator $validator;

    protected Router $router;

    protected ?string $routeConfig = null;

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    private function __construct(protected string $basePath)
    {
        $this->container = new Container();

        $this->templateEngine = new TemplateEngine($basePath);
        $this->response = new Response($this->templateEngine);
        $this->request = new Request();

        $this->redis = new Redis();
        $this->cache = new Cache($this->redis);

        $this->database = new Database();

        $this->validator = new Validator(new Translator(
            new FileLoader(),
            "tr",
        ));

        $this->router = new Router($this->container);
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
        $this->database->connect($host, $port, $dbname, $charset, $username, $password);
        return $this;
    }

    // --------------------------------------------------------------------------
    // RUN
    // --------------------------------------------------------------------------

    public function run(): void
    {
        ob_start();

        $this->container->instance(Response::class, $this->response);
        $this->container->instance(Cache::class, $this->cache);
        $this->container->instance(Database::class, $this->database);
        $this->container->instance(Validator::class, $this->validator);

        // Route Yapılandırmasını Yap
        if ($this->routeConfig !== null && method_exists($this->routeConfig, "register")) {
            $this->container->call([$this->routeConfig, "register"], ["router" => $this->router]);
        }
        // Controller'ı Çağır
        $response = $this->router->dispatch($this->request);

        // Bufferdakileri Çöpe At
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Response'u Göster
        $response->send();
    }
}
