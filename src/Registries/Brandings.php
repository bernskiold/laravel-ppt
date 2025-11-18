<?php

namespace BernskioldMedia\LaravelPpt\Registries;

class Brandings
{
    /**
     * Registered brandings.
     *
     * Brandings can be registered in a service provider's boot() method:
     * Brandings::register(['MyBrand' => MyBranding::class]);
     *
     * @var array<string, class-string>
     */
    public static array $brandings = [];

    /**
     * Get all registered brandings.
     *
     * @return array<string, class-string> Map of branding names to class names
     */
    public static function all(): array
    {
        return static::$brandings;
    }

    /**
     * Get available branding names.
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_keys(static::$brandings);
    }

    /**
     * Check if a branding exists.
     */
    public static function exists(string $name): bool
    {
        return isset(static::$brandings[$name]);
    }

    /**
     * Get the class name for a branding.
     *
     * @return class-string|null
     */
    public static function getClass(string $name): ?string
    {
        return static::$brandings[$name] ?? null;
    }

    /**
     * Register multiple brandings at once.
     *
     * This is typically called in a service provider's boot() method:
     *
     * Brandings::register([
     *     MyBranding::class,
     *     AnotherBranding::class,
     * ]);
     *
     * The label() method on each class will be used as the registry key.
     *
     * @param  array<class-string>  $brandings  Array of branding class names
     */
    public static function register(array $brandings): void
    {
        // Convert class names to label => class format
        $normalized = [];
        foreach ($brandings as $class) {
            $normalized[$class::label()] = $class;
        }

        static::$brandings = array_merge(
            static::$brandings,
            $normalized
        );
    }

    /**
     * Unregister one or more brandings by name.
     *
     * Brandings::unregister(['MyBrand', 'AnotherBrand']);
     *
     * @param  array<string>  $names  Array of branding names to remove
     */
    public static function unregister(array $names): void
    {
        foreach ($names as $name) {
            unset(static::$brandings[$name]);
        }
    }

    /**
     * Clear all registered brandings (primarily for testing).
     */
    public static function clear(): void
    {
        static::$brandings = [];
    }
}
