# Framework
> Modern PHP uygulamaları için geliştirilmiş hafif ve sade bir MVC framework çekirdeği.

## Açıklama
Modern PHP uygulamaları geliştirmek için oluşturulmuş hafif, sade ve geliştirici dostu bir MVC framework'üdür.

Framework; yalnızca ihtiyaç duyulan temel bileşenleri sağlayarak gereksiz karmaşıklıklardan kaçınır. Attribute tabanlı routing, middleware desteği, Dependency Injection Container, otomatik request validation ve authorization kontrolleri, session yönetimi, flash mesaj sistemi, cache ve veri tabanı gibi modern web uygulamalarında sık kullanılan özellikleri sunar.

Büyük framework'lerde zamanla oluşan karmaşık yapıların aksine, küçük ve anlaşılabilir bir çekirdek üzerine inşa edilmiştir. Bu sayede framework'ün çalışma mantığını öğrenmek, özelleştirmek ve ihtiyaçlara göre genişletmek oldukça kolaydır.

Bu framework'ün amacı, geliştiricilere esneklikten ödün vermeden hızlı geliştirme imkânı sunmak ve modern PHP uygulamaları için sağlam bir temel oluşturmaktır.

## Özellikler

- MVC mimarisi
- Dependency Injection Container
- Constructor Injection & Method Injection
- Klasik Routing & Attribute Routing
- Middleware sistemi
- Request / Response abstraction
- UploadedFile abstraction
- Session & Flash Messages
- Redis Cache
- PDO Database katmanı
- Repository Pattern
- Fluent API
- Route bazlı schema validation sistemi

## Kurulum
```bash
composer require seymenkonuk/framework
```

## Kullanım
### Klasik Routing
```php
$router->get("/", [HomeController::class, "index"]);
```

### Attribute Routing
```php
class HomeController extends Controller {
    #[Get("/")]
    public function index(Response $response){
        return $response->view("home");
    }
}
```

### Dependency Injection
```php
class UserController extends Controller { 
    public function __construct(
        private UserRepository $users,
    ) {} 
    
    public function index(Response $response)
    { 
        return $response->json(
            $this->users->all()
        ); 
    } 
}
``` 

### Dosya Yükleme
```php
$path = $request
            ->file("avatar")
            ->move("path");
```

## Route Bazlı Şema (Validation + Authorization)
### Şema Tanımı
```php
class ExampleSchema extends Schema
{
    public function __construct(
        protected Validator $validator
    ) {}

    public function body(): ObjectValidator
    {
        return $this->validator->object()->schema([
            "username" => $this->validator->field()
                ->string()
                ->min(3)
                ->required(),

            "email" => $this->validator->field()
                ->email()
                ->required(),
        ]);
    }

    public function query(): ObjectValidator
    {
        return $this->validator->object()->schema([]);
    }

    public function params(): ObjectValidator
    {
        return $this->validator->object()->schema([]);
    }

    public function files(): ObjectValidator
    {
        return $this->validator->object()->schema([]);
    }

    public function authorize(): ?array
    {
        if (true) {
            return [
                "title" => "Unauthorized",
                "description" => "Bu işlem için giriş yapmalısınız."
            ];
        }

        return null;
    }
}
```

### Route Üzerinde Kullanımı
```php
$router->post(
    "/users/{id}",
    [UserController::class, "update"],
)->schema(ExampleSchema::class);
```

## Lisans
Bu proje [MIT Lisansı](https://github.com/seymenkonuk/framework/blob/main/LICENSE) ile lisanslanmıştır.
