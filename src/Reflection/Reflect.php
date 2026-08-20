<?php
// ============================================================================
// File:    Reflect.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Reflection;


use Closure;
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
    // HANDLER
    // --------------------------------------------------------------------------

    /**
     * Belirtilen handler için reflection nesnesi döndürür.
     *
     * @param Closure|array{class-string, string} $handler reflection nesnesi oluşturulacak handler.
     *
     * @return ReflectionFunction|ReflectionMethod handler'ın reflection nesnesi.
     */
    public static function handler(Closure|array $handler): ReflectionFunction|ReflectionMethod
    {
        if ($handler instanceof Closure) {
            return self::closure($handler);
        }

        return self::method($handler[0], $handler[1]);
    }

    // --------------------------------------------------------------------------
    // INVOKE
    // --------------------------------------------------------------------------

    /**
     * Belirtilen callback'ı verilen parametrelerle çalıştırır.
     *
     * @template T
     *
     * @param Closure(mixed...): T $callback çalıştırılacak callback.
     * @param array<int, mixed> $parameters callback'a aktarılacak parametreler.
     *
     * @return T callback tarafından döndürülen değer.
     */
    public static function invoke(Closure $callback, array $parameters): mixed
    {
        $reflection = self::closure($callback);
        return $reflection->invokeArgs($parameters);
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
