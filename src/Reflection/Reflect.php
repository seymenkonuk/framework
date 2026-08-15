<?php
// ============================================================================
// File:    Reflect.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Reflection;


use Closure;
use LogicException;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;


final class Reflect
{
    // --------------------------------------------------------------------------
    // CLASS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen sınıf için reflection nesnesi döndürür.
     *
     * @template T of object
     * 
     * @param class-string<T>|T $class reflection nesnesi oluşturulacak sınıf.
     *
     * @return ReflectionClass<T> sınıfın reflection nesnesi.
     */
    public static function class(string|object $class): ReflectionClass
    {
        return new ReflectionClass($class);
    }

    // --------------------------------------------------------------------------
    // CONSTRUCTOR
    // --------------------------------------------------------------------------

    /**
     * Belirtilen sınıfın constructor'ı için reflection nesnesi döndürür.
     *
     * Sınıfın constructor'ı bulunmuyorsa null döndürülür.
     *
     * @template T of object
     * 
     * @param class-string<T>|T $class constructor'ı alınacak sınıf.
     *
     * @return ?ReflectionMethod constructor reflection nesnesi veya null.
     */
    public static function constructor(string|object $class): ?ReflectionMethod
    {
        return self::class($class)->getConstructor();
    }

    // --------------------------------------------------------------------------
    // METHOD
    // --------------------------------------------------------------------------

    /**
     * Belirtilen sınıf metodu için reflection nesnesi döndürür.
     *
     * @param object|class-string $class metodun bulunduğu sınıf.
     * @param string $method reflection nesnesi oluşturulacak metodun adı.
     *
     * @return ReflectionMethod metodun reflection nesnesi.
     */
    public static function method(string|object $class, string $method): ReflectionMethod
    {
        return new ReflectionMethod($class, $method);
    }

    // --------------------------------------------------------------------------
    // CLOSURE
    // --------------------------------------------------------------------------

    /**
     * Belirtilen closure için reflection nesnesi döndürür.
     *
     * @param Closure $function reflection nesnesi oluşturulacak closure.
     *
     * @return ReflectionFunction closure'ın reflection nesnesi.
     */
    public static function closure(Closure $function): ReflectionFunction
    {
        return new ReflectionFunction($function);
    }

    // --------------------------------------------------------------------------
    // CALLABLE
    // --------------------------------------------------------------------------

    /**
     * Belirtilen callable için reflection nesnesi döndürür.
     *
     * @param callable|array{class-string, string} $callable reflection nesnesi oluşturulacak callable.
     *
     * @return ReflectionFunction|ReflectionMethod callable'ın reflection nesnesi.
     */
    public static function callable(callable|array $callable): ReflectionFunction|ReflectionMethod
    {
        // function() {} gibi closure'ler
        if ($callable instanceof Closure) {
            return self::closure($callable);
        }

        // "strlen" gibi fonksiyon isimleri
        if (is_string($callable) && !str_contains($callable, "::")) {
            return new ReflectionFunction($callable);
        }

        // __invoke metodu bulunan bir objeler
        if (is_object($callable)) {
            return self::method($callable, "__invoke");
        }

        // [Class::name, $methodName] veya [$object, $methodName] gibi metotlar
        if (is_array($callable)) {
            return self::method($callable[0], $callable[1]);
        }

        // "DenemeClass::methodName" gibi metot isimleri
        if (is_string($callable) && str_contains($callable, "::")) {
            return new ReflectionMethod($callable);
        }

        // Yukarıdaki İhtimaller Tüm Durumları Zaten Kapsıyor
        throw new LogicException("Unsupported callable type.");
    }

    // --------------------------------------------------------------------------
    // INVOKE
    // --------------------------------------------------------------------------

    /**
     * Belirtilen callable'ı verilen parametrelerle çalıştırır.
     *
     * @template T
     *
     * @param callable(mixed...): T $callable çalıştırılacak callable.
     * @param array<int, mixed> $parameters callable'a aktarılacak parametreler.
     *
     * @return T callable tarafından döndürülen değer.
     */
    public static function invoke(callable $callable, array $parameters): mixed
    {
        $reflection = self::callable($callable);

        // Fonksiyonu Çağır
        if ($reflection instanceof ReflectionFunction) {
            return $reflection->invokeArgs($parameters);
        }

        // Objenin __invoke Metodunu Çağır
        if (is_object($callable)) {
            return $reflection->invokeArgs($callable, $parameters);
        }

        // Class'a Ait Bir Metotsa
        if (is_array($callable)) {
            // Statik Metotsa Null Verilmeli Değilse Obje
            $object = is_object($callable[0]) ? $callable[0] : null;
            return $reflection->invokeArgs($object, $parameters);
        }

        // "DenemeClass::methodName" gibi metot isimleri
        if (is_string($callable) && str_contains($callable, "::")) {
            return $reflection->invokeArgs(null, $parameters);
        }

        // Yukarıdaki İhtimaller Tüm Durumları Zaten Kapsıyor
        throw new LogicException("Unsupported callable type.");
    }

    // --------------------------------------------------------------------------
    // INSTANTIATION
    // --------------------------------------------------------------------------

    /**
     * Belirtilen sınıfın yeni bir nesne örneğini oluşturur.
     *
     * @template T of object
     *
     * @param class-string<T> $class oluşturulacak sınıf.
     * @param array<int, mixed> $parameters constructor'a aktarılacak parametreler.
     *
     * @return T oluşturulan nesne örneği.
     */
    public static function new(string $class, array $parameters = []): object
    {
        return self::class($class)->newInstanceArgs($parameters);
    }
}
