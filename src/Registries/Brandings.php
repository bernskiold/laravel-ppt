<?php

namespace BernskioldMedia\LaravelPpt\Registries;

class Brandings
{
    /**
     * Application-registered brandings via service provider.
     *
     * Applications can register custom brandings in their service provider:
     * BrandingRegistry::register(['MyBrand' => MyBranding::class]);
     *
     * @var array<string, class-string>
     */
    public static array $appBrandings = [];

    /**
     * Package's built-in brandings.
     *
     * Populated by the service provider during boot.
     * Currently empty - the package provides no default brandings.
     * Applications are expected to provide their own branding implementations.
     *
     * @var array<string, class-string>
     */
    protected static array $packageBrandings = [];

    /**
     * Register package brandings (called by service provider).
     *
     * @param  array<string, class-string>  $brandings  Map of names to class names
     */
    public static function registerPackage(array $brandings): void
    {
        static::$packageBrandings = array_merge(
            static::$packageBrandings,
            $brandings
        );
    }

    /**
     * Get all registered brandings (package + app combined).
     *
     * @return array<string, class-string> Map of branding names to class names
     */
    public static function all(): array
    {
        return array_merge(
            static::$packageBrandings,
            static::$appBrandings
        );
    }

    /**
     * Get available branding names.
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_keys(static::all());
    }

    /**
     * Check if a branding exists.
     */
    public static function exists(string $name): bool
    {
        return isset(static::all()[$name]);
    }

    /**
     * Get the class name for a branding.
     *
     * @return class-string|null
     */
    public static function getClass(string $name): ?string
    {
        return static::all()[$name] ?? null;
    }

    /**
     * Register multiple brandings at once.
     *
     * This is typically called in a service provider's boot() method:
     *
     * BrandingRegistry::register([
     *     'MyBrand' => MyBranding::class,
     *     'AnotherBrand' => AnotherBranding::class,
     * ]);
     *
     * @param  array<string, class-string>  $brandings  Map of names to class names
     */
    public static function register(array $brandings): void
    {
        static::$appBrandings = array_merge(
            static::$appBrandings,
            $brandings
        );
    }

    /**
     * Clear all registered brandings (primarily for testing).
     */
    public static function clear(): void
    {
        static::$packageBrandings = [];
        static::$appBrandings = [];
    }
}
