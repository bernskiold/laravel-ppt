<?php

namespace BernskioldMedia\LaravelPpt\Registries;

class Brandings
{
    /**
     * Registered branding classes.
     *
     * Brandings can be registered in a service provider's boot() method:
     * Brandings::register([MyBranding::class, AnotherBranding::class]);
     *
     * @var array<class-string>
     */
    public static array $brandings = [];

    /**
     * Get all registered brandings with their labels.
     *
     * @return array<string, class-string> Map of branding labels to class names
     */
    public static function all(): array
    {
        $result = [];
        foreach (static::$brandings as $class) {
            $result[$class::label()] = $class;
        }

        return $result;
    }

    /**
     * Get available branding labels.
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_keys(static::all());
    }

    /**
     * Check if a branding exists by label.
     */
    public static function exists(string $label): bool
    {
        foreach (static::$brandings as $class) {
            if ($class::label() === $label) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the class name for a branding by label.
     *
     * @return class-string|null
     */
    public static function getClass(string $label): ?string
    {
        foreach (static::$brandings as $class) {
            if ($class::label() === $label) {
                return $class;
            }
        }

        return null;
    }

    /**
     * Get all registered branding classes (raw list).
     *
     * @return array<class-string>
     */
    public static function classes(): array
    {
        return static::$brandings;
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
     * @param  array<class-string>  $brandings  Array of branding class names
     */
    public static function register(array $brandings): void
    {
        static::$brandings = array_merge(
            static::$brandings,
            $brandings
        );
    }

    /**
     * Unregister one or more brandings by class name.
     *
     * Brandings::unregister([MyBranding::class, AnotherBranding::class]);
     *
     * @param  array<class-string>  $classes  Array of branding class names to remove
     */
    public static function unregister(array $classes): void
    {
        static::$brandings = array_filter(
            static::$brandings,
            fn ($class) => ! in_array($class, $classes, true)
        );

        // Re-index the array to maintain sequential numeric keys
        static::$brandings = array_values(static::$brandings);
    }

    /**
     * Clear all registered brandings (primarily for testing).
     */
    public static function clear(): void
    {
        static::$brandings = [];
    }
}
