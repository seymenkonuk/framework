<?php
// ============================================================================
// File:    AttributeResolver.php
// Author:  Recep Seymen Konuk <konukrecepseymen@gmail.com>
//
// Licensed under the terms of the LICENSE file in the project root directory.
// ============================================================================

namespace Seymenkonuk\Framework\Reflection;


use ReflectionAttribute;
use ReflectionClass;
use ReflectionFunctionAbstract;


final class AttributeResolver
{
    // --------------------------------------------------------------------------
    // CONSTANTS
    // --------------------------------------------------------------------------

    /**
     * Attribute aramasında yalnızca belirtilen attribute sınıfının kullanılacağını
     * belirtir.
     *
     * @var int
     */
    public const DEFAULT_FLAGS = 0;

    /**
     * Attribute aramasında belirtilen sınıfın alt sınıflarının da dikkate
     * alınacağını belirtir.
     *
     * @var int
     */
    public const IS_INSTANCEOF = ReflectionAttribute::IS_INSTANCEOF;

    // --------------------------------------------------------------------------
    // REFLECTION
    // --------------------------------------------------------------------------

    /**
     * Belirtilen reflection nesnesindeki attribute'un tek örneğini döndürür.
     *
     * Attribute bulunamazsa null döndürülür.
     *
     * @template T of object
     * @template R of object
     *
     * @param ReflectionClass<R>|ReflectionFunctionAbstract $reflection
     *        attribute'u aranacak reflection nesnesi.
     * @param class-string<T> $attribute aranacak attribute sınıfı.
     * @param int $flags attribute aramasında kullanılacak Reflection flag'leri.
     *
     * @return T|null attribute örneği veya null.
     */
    public static function one(
        ReflectionClass|ReflectionFunctionAbstract $reflection,
        string $attribute,
        int $flags = self::DEFAULT_FLAGS
    ): ?object {
        $attributes = $reflection->getAttributes($attribute, $flags);

        if ($attributes === []) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    /**
     * Belirtilen reflection nesnesindeki tüm attribute örneklerini döndürür.
     *
     * Attribute bulunamazsa boş array döndürülür.
     *
     * @template T of object
     * @template R of object
     *
     * @param ReflectionClass<R>|ReflectionFunctionAbstract $reflection
     *        attribute'ları aranacak reflection nesnesi.
     * @param class-string<T> $attribute aranacak attribute sınıfı.
     * @param int $flags attribute aramasında kullanılacak Reflection flag'leri.
     *
     * @return array<int, T> attribute örnekleri.
     */
    public static function all(
        ReflectionClass|ReflectionFunctionAbstract $reflection,
        string $attribute,
        int $flags = self::DEFAULT_FLAGS
    ): array {
        return array_map(
            fn($attribute) => $attribute->newInstance(),
            $reflection->getAttributes($attribute, $flags)
        );
    }

    // --------------------------------------------------------------------------
    // METHODS
    // --------------------------------------------------------------------------

    /**
     * Belirtilen metodun attribute örneğini döndürür.
     *
     * Öncelikle metodun attribute'ları kontrol edilir. Belirtilen attribute
     * metotta bulunamazsa sınıfın attribute'ları kontrol edilir.
     *
     * Hem metotta hem de sınıfta attribute bulunamazsa null döndürülür.
     *
     * @template T of object
     *
     * @param class-string $class metodun bulunduğu sınıf.
     * @param string $method attribute'u aranacak metodun adı.
     * @param class-string<T> $attribute aranacak attribute sınıfı.
     * @param int $flags attribute aramasında kullanılacak Reflection flag'leri.
     *
     * @return T|null attribute örneği veya null.
     */
    public static function resolve(
        string $class,
        string $method,
        string $attribute,
        int $flags = self::DEFAULT_FLAGS
    ): ?object {
        $classReflection = Reflect::class($class);
        $methodReflection = Reflect::method($class, $method);

        return self::one($methodReflection, $attribute, $flags)
            ?? self::one($classReflection, $attribute, $flags);
    }

    /**
     * Belirtilen metodun ve sınıfın tüm attribute örneklerini döndürür.
     *
     * Sınıfa ait attribute'lar ile metoda ait attribute'lar birleştirilerek
     * döndürülür.
     *
     * @template T of object
     *
     * @param class-string $class metodun bulunduğu sınıf.
     * @param string $method attribute'ları alınacak metodun adı.
     * @param class-string<T> $attribute aranacak attribute sınıfı.
     * @param int $flags attribute aramasında kullanılacak Reflection flag'leri.
     *
     * @return array<int, T> sınıfa ve metoda ait attribute örnekleri.
     */
    public static function resolveAll(
        string $class,
        string $method,
        string $attribute,
        int $flags = self::DEFAULT_FLAGS
    ): array {
        $classReflection = Reflect::class($class);
        $methodReflection = Reflect::method($class, $method);

        return [
            ...self::all($classReflection, $attribute, $flags),
            ...self::all($methodReflection, $attribute, $flags),
        ];
    }
}
