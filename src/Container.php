<?php
// ============================================================================
// File:    Container.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework;


use Closure;

use ReflectionNamedType;
use ReflectionParameter;

use RuntimeException;

use Seymenkonuk\Framework\Reflection\Reflect;


final class Container
{
    // --------------------------------------------------------------------------
    // PROPERTIES
    // --------------------------------------------------------------------------

    /**
     * İki sınıf arasındaki bağlantıları saklar.
     *
     * @var array<class-string, class-string>
     */
    private array $bindings = [];

    /**
     * Sınıfa ait nesne örneklerini saklar.
     *
     * @var array<class-string, object>
     */
    private array $instances = [];

    /**
     * Sınıfa ait nesne örneğini üreten işlevleri saklar.
     *
     * @var array<class-string, Closure(mixed...): object>
     */
    private array $singletons = [];

    // --------------------------------------------------------------------------
    // BINDINGS
    // --------------------------------------------------------------------------

    /**
     * Bir sınıfı başka bir sınıfa bağlar.
     *
     * Bu kayıt sonrasında ilgili sınıf talep edildiğinde bağlantılı
     * sınıf döndürülür.
     *
     * Aynı sınıf için daha önce bağlantı tanımlanmışsa mevcut kayıt güncellenir.
     *
     * @template T of object
     * 
     * @param class-string<T> $abstract çözümlenecek sınıf.
     * @param class-string<T> $concrete oluşturulacak sınıf.
     *
     * @return void
     */
    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Belirtilen sınıf için hazır bir nesne örneği kaydeder.
     *
     * Bu kayıt sonrasında ilgili sınıf talep edildiğinde her zaman
     * aynı nesne örneği döndürülür.
     *
     * Daha önce kayıt edilmiş bir örnek varsa üzerine yazılır.
     * 
     * @template T of object
     *
     * @param class-string<T> $class ilişkilendirilecek sınıf.
     * @param T $instance kullanılacak nesne örneği.
     *
     * @return void
     */
    public function instance(string $class, object $instance): void
    {
        $this->instances[$class] = $instance;
    }

    /**
     * Belirtilen sınıf için nesne örneği döndüren Closure kaydeder.
     * 
     * Bu kayıt sonrasında ilgili sınıf ilk kez talep edildiğinde işlev
     * çalıştırılır, oluşturulan nesne örneği saklanır ve döndürülür. 
     * Sonraki taleplerde aynı nesne örneği döndürülür.
     *
     * @template T of object
     *
     * @param class-string<T> $class çözümlenecek sınıf.
     * @param Closure(mixed...): T $factory nesne örneğini oluşturacak işlev.
     *
     * @return void
     */
    public function singleton(string $class, Closure $factory): void
    {
        $this->singletons[$class] = $factory;
    }

    // --------------------------------------------------------------------------
    // RESOLUTION
    // --------------------------------------------------------------------------

    /**
     * Belirtilen sınıf için bir nesne örneği döndürür.
     *
     * Kayıtlı bir örnek mevcutsa doğrudan döndürülür.
     * Aksi halde ilgili somut sınıf oluşturulur ve bağımlılıkları çözülür.
     * 
     * @template T of object
     *
     * @param class-string<T> $class oluşturulacak veya çözümlenecek sınıf.
     *
     * @return T oluşturulan veya mevcut nesne örneği.
     */
    public function make(string $class): object
    {
        // Bağlantılı sınıfı al.
        $class = $this->resolveBinding($class);

        // Daha önce oluşturulmuş nesne örneğini al.
        $instance = $this->getInstance($class);
        if (isset($instance)) {
            return $instance;
        }

        // Singleton factory'sini al.
        $factory = $this->getSingleton($class);
        if (isset($factory)) {
            // Instance oluştur
            $instance = $this->call($factory);
            // Instance'ı kaydet
            $this->instance($class, $instance);
            // Instance'ı dön
            return $instance;
        }

        // Sınıfı bağımlılıklarını çözümleyerek oluştur.
        return $this->build($class);
    }

    /**
     * Belirtilen sınıfın container tarafından çözümlenip çözümlenemeyeceğini
     * kontrol eder.
     *
     * @template T of object
     *
     * @param class-string<T> $class kontrol edilecek sınıf.
     *
     * @return bool sınıf çözümlenebiliyorsa true, aksi halde false.
     */
    public function canMake(string $class): bool
    {
        // Bağlantılı sınıfı al.
        $class = $this->resolveBinding($class);

        // Daha önce oluşturulmuş nesne örneği mevcutsa çözümlenebilir.
        $instance = $this->getInstance($class);
        if (isset($instance)) {
            return true;
        }

        // Singleton factory'si varsa ve çağrılabiliyorsa çözümlenebilir.
        $factory = $this->getSingleton($class);
        if (isset($factory)) {
            return $this->canCall($factory);
        }

        // Sınıfın oluşturulup oluşturulamayacağını kontrol et.
        return $this->canBuild($class);
    }

    // --------------------------------------------------------------------------
    // INVOCATION
    // --------------------------------------------------------------------------

    /**
     * Belirtilen işlevi verilen parametrelerle çalıştırır.
     *
     * İşlev tarafından ihtiyaç duyulan parametreler container üzerinden
     * çözümlenebilir.
     *
     * $parameters ile verilen değerler işleve aktarılacak parametreler
     * için kullanılır.
     *
     * @template T
     *
     * @param Closure(mixed...): T $callback çalıştırılacak işlev.
     * @param array<string, mixed> $parameters işleve aktarılacak parametreler.
     *
     * @return T işlevin döndürdüğü değer
     */
    public function call(Closure $callback, array $parameters = []): mixed
    {
        $reflection = Reflect::closure($callback);

        $dependencies = $this->resolveParameters(
            $reflection->getParameters(),
            $parameters,
        );

        return Reflect::invoke($callback, $dependencies);
    }

    /**
     * Belirtilen işlevin verilen parametrelerle çalıştırılıp çalıştırılamayacağını
     * kontrol eder.
     *
     * @template T
     *
     * @param Closure(mixed...): T $callback kontrol edilecek işlev.
     * @param array<string, mixed> $parameters işleve aktarılacak parametreler.
     *
     * @return bool işlev çalıştırılabiliyorsa true, aksi halde false.
     */
    public function canCall(Closure $callback, array $parameters = []): bool
    {
        $reflection = Reflect::closure($callback);

        return $this->canResolveParameters(
            $reflection->getParameters(),
            $parameters,
        );
    }

    // --------------------------------------------------------------------------
    // INTERNAL
    // --------------------------------------------------------------------------

    /**
     * Kayıtlı nesne örneğini döndürür.
     *
     * @template T of object
     *
     * @param class-string<T> $class nesne örneğinin alınacağı sınıf.
     *
     * @return ?T kayıtlı nesne örneği veya null.
     */
    private function getInstance(string $class): ?object
    {
        // @phpstan-ignore return.type
        return $this->instances[$class] ?? null;
    }

    /**
     * Sınıf için kayıtlı bağlantıyı döndürür.
     *
     * @template T of object
     *
     * @param class-string<T> $class bağlantısı alınacak sınıf.
     *
     * @return class-string<T> bağlantılı sınıf veya verilen sınıf.
     */
    private function getBinding(string $class): string
    {
        // @phpstan-ignore return.type
        return $this->bindings[$class] ?? $class;
    }

    /**
     * Sınıf için kayıtlı bağlantı olup olmadığını döndürür.
     *
     * @template T of object
     *
     * @param class-string<T> $class kontrol edilecek sınıf.
     *
     * @return bool binding tanımlanmışsa true, aksi halde false.
     */
    private function hasBinding(string $class): bool
    {
        return array_key_exists($class, $this->bindings);
    }

    /**
     * Sınıf için tanımlanmış binding zincirini çözer.
     *
     * @template T of object
     *
     * @param class-string<T> $class çözümlenecek sınıf.
     *
     * @throws RuntimeException döngüsel binding zinciri tespit edildiğinde hata fırlatılır.
     * 
     * @return class-string<T> binding zincirinin sonunda bulunan sınıf.
     */
    private function resolveBinding(string $class): string
    {
        $resolved = [];

        while ($this->hasBinding($class)) {
            // Daha Önce Bu Sınıfla Zaten Karşılaştıysan Döngü Vardır
            if (isset($resolved[$class])) {
                throw new RuntimeException("Circular binding detected for {$class}.");
            }

            // Döngü Tespiti için Bu Sınıfı Kaydet
            $resolved[$class] = true;

            // Bağlantılı Sınıfı Al
            $class = $this->getBinding($class);
        }

        return $class;
    }

    /**
     * Belirtilen sınıf için kayıtlı singleton factory'yi döndürür.
     *
     * @template T of object
     *
     * @param class-string<T> $class singleton factory'si alınacak sınıf.
     *
     * @return (Closure(mixed...): T)|null kayıtlı singleton factory veya null.
     */
    private function getSingleton(string $class): ?Closure
    {
        // @phpstan-ignore return.type
        return $this->singletons[$class] ?? null;
    }

    /**
     * Belirtilen sınıftan bir nesne oluşturmaya çalışır.
     *
     * Sınıfın constructor bağımlılıkları container üzerinden çözülür.
     * 
     * @template T of object
     *
     * @param class-string<T> $class oluşturulacak sınıf.
     *
     * @return T oluşturulan nesne örneği.
     */
    private function build(string $class): object
    {
        $constructor = Reflect::constructor($class);

        $dependencies = $this->resolveParameters(
            $constructor?->getParameters() ?? [],
        );

        return Reflect::new($class, $dependencies);
    }

    /**
     * Belirtilen sınıftan bir nesne oluşturulup oluşturulamayacağını kontrol eder.
     *
     * Sınıfın constructor bağımlılıklarının container üzerinden çözülüp
     * çözülemeyeceği kontrol edilir.
     * 
     * @template T of object
     *
     * @param class-string<T> $class kontrol edilecek sınıf.
     *
     * @return bool sınıf oluşturulabiliyorsa true, aksi halde false.
     */
    private function canBuild(string $class): bool
    {
        $reflection = Reflect::class($class);

        // Sınıf oluşturulabilir değilse bağımlılık çözümlenemez.
        if (! $reflection->isInstantiable()) {
            return false;
        }

        $constructor = Reflect::constructor($class);

        return $this->canResolveParameters(
            $constructor?->getParameters() ?? [],
        );
    }

    /**
     * Belirtilen reflection parametreleri için gerekli bağımlılıkları çözümler.
     *
     * Sağlanan parametreler öncelikli olarak kullanılır; eksik parametreler
     * container üzerinden çözümlenir.
     *
     * @param array<ReflectionParameter> $parameters çözümlenecek parametreler.
     * @param array<string, mixed> $provided dışarıdan sağlanan parametreler.
     *
     * @return array<int, mixed> çözümlenen parametre değerleri.
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

            if ($parameter->isDefaultValueAvailable()) {
                $resolved[] = $parameter->getDefaultValue();
                continue;
            }

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                /** @var class-string */
                $className = $type->getName();
                $resolved[] = $this->make($className);
                continue;
            }

            throw new RuntimeException("Unable to resolve parameter: " . $parameter->getName() . "!");
        }

        return $resolved;
    }

    /**
     * Belirtilen reflection parametrelerinin çözümlenip
     * çözümlenemeyeceğini kontrol eder.
     *
     * Sağlanan parametreler öncelikli olarak kullanılır; eksik parametreler
     * container üzerinden çözümlenir.
     *
     * @param array<ReflectionParameter> $parameters kontrol edilecek parametreler.
     * @param array<string, mixed> $provided dışarıdan sağlanan parametreler.
     *
     * @return bool tüm parametreler çözümlenebiliyorsa true, aksi halde false.
     */
    private function canResolveParameters(array $parameters, array $provided = []): bool
    {
        foreach ($parameters as $parameter) {
            // Dışarıdan sağlanan parametrelerin çözümlenmesine gerek yok.
            if (array_key_exists($parameter->getName(), $provided)) {
                continue;
            }

            // Varsayılan değeri olan parametrelerin çözümlenmesine gerek yok.
            if ($parameter->isDefaultValueAvailable()) {
                continue;
            }

            $type = $parameter->getType();

            // Sınıf bağımlılığı değilse çözümlenemez.
            if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
                return false;
            }

            /** @var class-string */
            $className = $type->getName();

            // Bağımlılık çözümlenemiyorsa parametreler çözümlenemez.
            if (!$this->canMake($className)) {
                return false;
            }
        }

        return true;
    }
}
