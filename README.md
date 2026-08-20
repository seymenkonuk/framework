# Framework
> Modern PHP uygulamaları için geliştirilmiş hafif, sade ve genişletilebilir bir framework.

## Açıklama
Hafif, sade ve geliştirici dostu bir PHP framework'üdür.

Framework; yalnızca ihtiyaç duyulan temel bileşenleri sağlayarak gereksiz karmaşıklıklardan kaçınır. Routing, Dependency Injection, middleware, request validation, template engine, session, flash, cache ve HTTP request/response yönetimi gibi modern web uygulamalarında sık kullanılan bileşenler sunar.

Büyük framework'lerde zamanla oluşan karmaşık yapıların aksine, küçük ve anlaşılabilir bir yapı üzerine inşa edilmiştir. Bu sayede framework'ün çalışma mantığını öğrenmek, özelleştirmek ve ihtiyaçlara göre genişletmek oldukça kolaydır.

Bu framework'ün amacı, geliştiricilere esneklikten ödün vermeden hızlı geliştirme imkânı sunmak ve modern PHP uygulamaları için sağlam bir temel oluşturmaktır.

## Özellikler

- Closure, Controller ve Attribute tabanlı Routing
- Route parametreleri, kısıtlamaları ve grupları
- Dependency Injection Container
- Constructor, Method ve Closure Injection
- Middleware sistemi
- Request Schema ile istek doğrulama
- Template Engine abstraction
- Request / Response abstraction
- UploadedFile abstraction
- Session ve Flash sistemi
- Cache sistemi
- CSRF Token yönetimi
- Database abstraction
- Global Exception Handler
- Fluent API

## Kurulum
```bash
composer require seymenkonuk/framework
```

## Klasik Routing 
### Route Tanımlama 
Route'lar HTTP metodu ve URI belirtilerek tanımlanabilir. 

Route handler olarak closure kullanılabilir:
```php
$router->get("/", function (IRequest $request, IResponse $response): IResponse {
    return $response->json($request->all());
});
```

Controller metotları da route handler olarak kullanılabilir:
```php
$router->get("/", [HomeController::class, "index"]);
```

> 💡 **Not:** Yalnızca statik olmayan metotlar route handler olarak kullanılabilir.

Desteklenen HTTP metodları için ilgili router metodları kullanılabilir:
```php
$router->get("/users", [UserController::class, "index"]);
$router->query("/users", [UserController::class, "query"]);
$router->post("/users", [UserController::class, "create"]);
$router->put("/users/{id}", [UserController::class, "update"]);
$router->patch("/users/{id}", [UserController::class, "update"]);
$router->delete("/users/{id}", [UserController::class, "delete"]);
$router->any("/health", [HealthController::class, "check"]);
```

### Route Parametreleri 
Route URI'sine `{}` içerisinde bir veya birden fazla parametre eklenebilir:
```php
$router->get("/users/{userCode}", function (string $userCode, IResponse $response): IResponse {
    return $response->text($userCode);
});
```

Route parametreleri handler'ın parametrelerine otomatik olarak aktarılır. Parametreleri handler üzerinden almak istemediğiniz durumlarda request üzerinden de erişilebilir:
```php
$router->get("/users/{userCode}", function (IRequest $request, IResponse $response): IResponse {
    return $response->text($request->param("userCode"));
});
```

Parametrelerin eşleşeceği değerler `where()` ile regex kullanılarak kısıtlanabilir:
```php
$router->get("/users/{userId}", function (int $userId, IResponse $response): IResponse {
    return $response->text("$userId");
})->where("userId", "[0-9]+");
```

Yaygın kullanılan kısıtlamalar için hazır where metodları da bulunur:
```php
$router->get("/users/{userId}", function (int $userId, IResponse $response): IResponse {
    return $response->text("$userId");
})->whereNumber("userId");
```

### Route Gruplama 
Ortak route ayarlarına sahip route'lar gruplandırılabilir. Grup içerisinde tanımlanan route'lar ortak prefix gibi ayarları paylaşabilir:
```php
$router->group(["prefix" => "/users"], function (Router $router): void {
    $router->query("/", [UserController::class, "query"]);
    $router->post("/", [UserController::class, "create"]);
    $router->put("{id}", [UserController::class, "update"]);
    $router->patch("{id}", [UserController::class, "update"]);
    $router->delete("{id}", [UserController::class, "delete"]);
});
```

## Attribute Routing
### Route Tanımlama 
Route'lar controller ve metodlar üzerine attribute'lar eklenerek de tanımlanabilir:
```php
class UserController extends Controller
{
    #[Get("/users")]
    public function index(): IResponse
    {
        // ...
    }

    #[Post("/users")]
    public function create(): IResponse
    {
        // ...
    }
}
```

Controller tanımlandıktan sonra route'ların keşfedilebilmesi için controller router'a `registerController()` metodu ile kaydedilmelidir:
```php
$router->registerController(UserController::class);
```

### Route Parametreleri 
Attribute tabanlı route'larda da URI içerisinde dinamik parametreler kullanılabilir:
```php
class UserController extends Controller
{
    #[Get("/users/{userCode}")]
    public function show(string $userCode, IResponse $response): IResponse
    {
        return $response->text($userCode);
    }
}
```

Parametreler Where attribute'u kullanılarak regex ile kısıtlanabilir:
```php
class UserController extends Controller
{
    #[Get("/users/{userId}")]
    #[Where("userId", "[0-9]+")]
    public function show(int $userId, IResponse $response): IResponse
    {
        return $response->text("$userId");
    }
}
```

Yaygın kullanılan kurallar için hazır attribute'lar da kullanılabilir:
```php
class UserController extends Controller
{
    #[Get("/users/{userId}")]
    #[WhereNumber("userId")]
    public function show(int $userId, IResponse $response): IResponse
    {
        return $response->text("$userId");
    }
}
```

### Route Gruplama 
Controller üzerindeki Prefix attribute'u, controller'a ait route'ları ortak bir URI prefix'i altında gruplamak için kullanılabilir:
```php
#[Prefix("/users")]
class UserController extends Controller
{
    #[Get("/")]
    public function index(IResponse $response): IResponse
    {
        // ...
    }

    #[Post("/")]
    public function create(IResponse $response): IResponse
    {
        // ...
    }
}
```

Attribute'lar kullanılarak route'lara ortak başka ayarlar da uygulanabilir:
```php
#[Prefix("/admin")]
#[Middleware(AuthMiddleware::class)]
class AdminController extends Controller
{
    #[Get("/users")]
    public function users(): IResponse
    {
        // ...
    }
    #[Get("/posts")]
    public function posts(): IResponse
    {
        // ...
    }
}
```

## Dependency Injection
Framework'ün DI Container'ı, bağımlılıkları otomatik olarak çözerek constructor, method veya closure parametrelerine aktarabilir.

### Constructor Injection
Bir sınıfın constructor'ında tanımlanan sınıf bağımlılıkları otomatik olarak çözülür:
```php
class ExampleController extends Controller
{
    public function __construct(
        private ISession $session,
        private IFlash $flash,
        private ICache $cache,
        // ...
    ) {}
}
``` 

### Method Injection
Metot parametrelerinde tanımlanan bağımlılıklar da container üzerinden çözülerek metoda aktarılır:
```php
class ExampleController extends Controller
{
    #[Get("/example")]
    public function index(ISession $session, IResponse $response): IResponse
    {
        return $response->json(
            $session->all(),
        );
    }
}
```

### Closure Injection
Route handler'da tanımlanan bağımlılıklar da container üzerinden çözülerek fonksiyona aktarılır:
```php
$router->get("/example", function (IFlash $flash, IResponse $response): IResponse {
    return $response->text(
        $flash->get("example", "example"),
    );
});
```

### Bağımlılıkları Tanımlama
Bağımlılıkların otomatik olarak çözülebilmesi için container'a bazı tanımlamalar yapılmalıdır.

Bir interface belirli bir sınıfa bağlanabilir. `IFlash` her istendiğinde yeni bir `SessionFlash` nesnesi oluşturulur:
```php
$app->withBindings([
    IFlash::class => SessionFlash::class,
]);
```

Hazır bir nesne container'a eklenebilir. `ISession` her istendiğinde eklenen aynı nesne kullanılır:
```php
$app->withInstances([
    ISession::class => new PhpSession(),
]);
```

Bir bağımlılık singleton olarak tanımlanabilir. `ITemplateEngine` ilk istendiğinde closure çalıştırılarak oluşturulur ve sonraki her istekte aynı nesne kullanılır:
```php
$app->withSingletons([
    ITemplateEngine::class => function (): PlatesTemplateEngine {
        return new PlatesTemplateEngine(__DIR__);
    },
]);
```

## Middleware
Middleware'ler, HTTP isteğinin işlenmesinden önce ve sonra çalıştırılabilen ara katmanlardır.

### Middleware Oluşturma
Middleware oluşturmak için Middleware abstract sınıfından türeyen bir sınıf tanımlanabilir:
```php
class ExampleMiddleware extends Middleware
{
    public function handle(IRequest $request, IResponse $response, Closure $next): IResponse
    {
        // istek işlenmeden önce yapılacaklar
        // ...

        $response = $next($request, $response);

        // istek işlendikten sonra yapılacaklar
        // ...

        return $response;
    }
}
```

### Middleware Kullanımı
Tüm route'larda çalışması gereken middleware'ler global olarak tanımlanabilir:
```php
$router->middleware([
    ExampleMiddleware::class,
]);
```

Bir route grubuna middleware eklenebilir:
```php
$router->group([
    "middleware" => [
        ExampleMiddleware::class,
    ],
], function (Router $router): void {
    // ...
});
```

Tek bir route'a middleware eklenebilir:
```php
$router->get("/", function (): IResponse {
    // ...
})->middleware(ExampleMiddleware::class);
```

Attribute routing'de Middleware attribute'u kullanılabilir:
```php
#[Middleware(ExampleMiddleware::class)]
class ExampleController extends Controller
{
    #[Get("/example")]
    #[Middleware(ExampleMiddleware::class)]
    public function index(): IResponse
    {
        // ...
    }
}
```

## Request Schema
`RequestSchema`, route'a gelen istek verilerinin doğrulanması ve route çalıştırılmadan önce kontrol edilmesi için kullanılır.

### Şema Oluşturma
Validator implementasyonundan bağımsız bir request schema oluşturmak için `IRequestSchema` interface'i implement edilebilir. Framework'ün validator altyapısını kullanmak için `ValidatorRequestSchema` sınıfından türetilebilir:
```php
class ExampleSchema extends ValidatorRequestSchema
{
    public function body(): ObjectValidator
    {
        return $this->validator->object()->schema([
            "name" => $this->validator->field()
                ->string()
                ->required(),
            "email" => $this->validator->field()
                ->email()
                ->required(),
        ]);
    }
}
```

### Şema Kullanımı
Klasik routing'de şema route'a `schema()` metodu ile eklenebilir:
```php
$router->get("/", function (): IResponse {
    // ...
})->schema(ExampleSchema::class);
```

Attribute routing'de ise Schema attribute'u kullanılabilir:
```php
class ExampleController extends Controller
{
    #[Get("/example")]
    #[Schema(ExampleSchema::class)]
    public function index(): IResponse
    {
        // ...
    }
}
```

> 💡 **Not:** İstek doğrulanamazsa `ValidationException` fırlatılır.

## Template Engine
Framework, template engine kullanımını `ITemplateEngine` interface'i üzerinden soyutlar. Böylece uygulama belirli bir template engine implementasyonuna doğrudan bağımlı olmak zorunda kalmaz.

Template engine container'a tanımlanabilir:
```php
$app->withSingletons([
    ITemplateEngine::class => function (): PlatesTemplateEngine {
        return new PlatesTemplateEngine(__DIR__);
    },
]);
```

## Request
`IRequest`, HTTP isteğine ait verilere erişmek için kullanılır.

İsteğin HTTP metodu, URL ve istemci bilgilerine erişilebilir:
```php
$method = $request->method();
$path = $request->path();
$url = $request->url();
$ip = $request->ip();
```

Header ve cookie değerleri okunabilir:
```php
$token = $request->header("Authorization");
$session = $request->cookie("session");
```

İstek gövdesindeki verilere içerik türüne göre erişilebilir:
```php
$name = $request->post("name");
$email = $request->json("email");
```

Query ve route parametreleri ayrı olarak alınabilir:
```php
$page = $request->query("page", 1);
$userId = $request->param("userId");
```

Yüklenen dosyalara `file()` metodu ile erişilebilir:
```php
$file = $request->file("avatar");
$file?->move(__DIR__ . "/uploads/avatars");
```

İsteğe ait tüm veriler `all()` metodu ile alınabilir:
```php
$data = $request->all();
```

## Response
`IResponse`, HTTP response oluşturmak ve response özelliklerini değiştirmek için kullanılır.

Metin, HTML ve JSON response'ları oluşturulabilir:
```php
return $response->text("Framework.");

return $response->html("<h1>Framework!</h1>");

return $response->json([
    "message" => "Framework.",
]);
```

Response'un HTTP durum kodu değiştirilebilir:
```php
return $response->json([
    "message" => "Framework.",
])->status(201);
```

Başka bir URL'ye yönlendirme yapılabilir:
```php
return $response->redirect("/login");
```

Dosyalar response olarak gönderilebilir veya indirme response'u oluşturulabilir:
```php
return $response->file(__DIR__ . "/files/example.pdf");
return $response->download(__DIR__ . "/files/example.pdf", "document.pdf");
```

Response'a header veya cookie eklenebilir:
```php
return $response->header("X-Example", "value")
                ->cookie($cookie);
```

## Global Exception Handler
Uygulama içerisinde oluşan exception'lar için birden fazla global exception handler tanımlanabilir.

Handler closure'ında exception isimli bir parametre bulunmalıdır. Bu parametrenin tipi, handler'ın hangi exception'ları yakalayacağını belirler:
```php
$app->withException(function (ValidationException $exception, IRequest $request, IResponse $response): IResponse {
    return $response->json(
        $exception->errors(),
    );
});
$app->withException(function (Throwable $exception, IRequest $request, IResponse $response): IResponse {
    return $response->text("Bilinmeyen hata!");
});
```

## Kullanım
Framework ile yeni bir proje oluşturmak için iki farklı başlangıç repository'si kullanılabilir.

[`framework-starter`](https://github.com/seymenkonuk/framework-starter), framework ile küçük uygulamalar geliştirmek için minimal bir başlangıç projesidir:
```bash
composer create-project seymenkonuk/framework-starter example
```

Daha kapsamlı uygulamalar için [`framework-skeleton`](https://github.com/seymenkonuk/framework-skeleton) kullanılabilir. MVC yapısına uygun hazır klasör yapısı, örnek controller'lar ve Docker desteği sunar:
```bash
composer create-project seymenkonuk/framework-skeleton example
```

## Gelecek Planları

- [ ] MySQL, MSSQL, PostgreSQL gibi farklı veritabanı sistemlerinden bağımsız sorgular oluşturulabilmesini sağlamak amacıyla QueryBuilder geliştirilecek.

- [ ] Framework'ün bileşenlerinin doğru çalıştığını doğrulamak ve gelecekte yapılacak değişikliklerin mevcut davranışları bozmadığından emin olmak için kapsamlı bir test suite oluşturulacak.

- [ ] Framework'ü kullanan uygulamaların kendi testlerini daha kolay yazabilmesini sağlamak amacıyla bir `TestCase` sınıfı oluşturulacak. 

## Lisans
Bu proje [MIT Lisansı](https://github.com/seymenkonuk/framework/blob/main/LICENSE) ile lisanslanmıştır.
