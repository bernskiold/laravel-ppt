<?php

namespace BernskioldMedia\LaravelPpt\Registries;

use function collect;
use function interface_exists;
use function is_subclass_of;
use function method_exists;

class SlideMasters
{
    /**
     * Registered slide masters.
     *
     * Masters can be registered in a service provider's boot() method:
     * SlideMasters::register([CustomMaster::class]);
     *
     * @var array<class-string>
     */
    public static array $masters = [];

    /**
     * Get all registered slide masters with their metadata.
     *
     * Returns masters that implement DynamicallyCreatable (if interface exists).
     * Each master includes: class, description, schema, example.
     *
     * @return array<string, array>
     */
    public static function all(): array
    {
        return collect(static::$masters)
            ->filter(function ($class) {
                // Check if DynamicallyCreatable interface exists
                if (! interface_exists('BernskioldMedia\\LaravelPpt\\Contracts\\DynamicallyCreatable')) {
                    return true; // Return all if interface doesn't exist yet
                }

                return is_subclass_of($class, 'BernskioldMedia\\LaravelPpt\\Contracts\\DynamicallyCreatable');
            })
            ->mapWithKeys(fn ($class) => [
                $class::label() => static::buildMasterDefinition($class),
            ])
            ->all();
    }

    /**
     * Build master definition array with metadata.
     *
     * @param  class-string  $class
     */
    protected static function buildMasterDefinition(string $class): array
    {
        $definition = ['class' => $class];

        // Add schema info if available (from DynamicallyCreatable interface)
        if (method_exists($class, 'description')) {
            $definition['description'] = $class::description();
        }

        if (method_exists($class, 'dataSchema')) {
            $definition['schema'] = $class::dataSchema();
        }

        if (method_exists($class, 'exampleData')) {
            $definition['example'] = $class::exampleData();
        }

        return $definition;
    }

    /**
     * Get a specific slide master definition.
     */
    public static function get(string $masterName): ?array
    {
        return static::all()[$masterName] ?? null;
    }

    /**
     * Check if a slide master exists.
     */
    public static function exists(string $masterName): bool
    {
        return isset(static::all()[$masterName]);
    }

    /**
     * Get the class name for a slide master.
     *
     * @return class-string|null
     */
    public static function getClass(string $masterName): ?string
    {
        $master = static::get($masterName);

        return $master['class'] ?? null;
    }

    /**
     * Get available slide master names.
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_keys(static::all());
    }

    /**
     * Register multiple slide masters at once.
     *
     * This is typically called in a service provider's boot() method:
     *
     * SlideMasters::register([
     *     CustomMaster::class,
     *     AnotherMaster::class,
     * ]);
     *
     * @param  array<class-string>  $masters  Array of slide master class names
     */
    public static function register(array $masters): void
    {
        static::$masters = array_merge(
            static::$masters,
            $masters
        );
    }

    /**
     * Unregister one or more slide masters.
     *
     * Accepts either master names (e.g., 'Title', 'Blank With Title') or full class names.
     *
     * SlideMasters::unregister(['Title', 'Blank With Title', CustomMaster::class]);
     *
     * @param  array<string>  $names  Array of master names or class names to remove
     */
    public static function unregister(array $names): void
    {
        foreach ($names as $name) {
            // If it contains a backslash, treat it as a class name
            if (str_contains($name, '\\')) {
                static::$masters = array_filter(
                    static::$masters,
                    fn ($class) => $class !== $name
                );
            } else {
                // Otherwise, treat it as a master name (using label)
                static::$masters = array_filter(
                    static::$masters,
                    fn ($class) => $class::label() !== $name
                );
            }
        }

        // Re-index the array to maintain sequential numeric keys
        static::$masters = array_values(static::$masters);
    }

    /**
     * Get all registered master classes (raw list without metadata).
     *
     * @return array<class-string>
     */
    public static function classes(): array
    {
        return static::$masters;
    }

    /**
     * Clear all registered masters (primarily for testing).
     */
    public static function clear(): void
    {
        static::$masters = [];
    }
}
