<?php

namespace BernskioldMedia\LaravelPpt\Registries;

use function collect;
use function interface_exists;
use function is_subclass_of;
use function method_exists;

class Components
{
    /**
     * Registered components.
     *
     * Components can be registered in a service provider's boot() method:
     * Components::register([TextBox::class, Image::class]);
     *
     * @var array<class-string>
     */
    public static array $components = [];

    /**
     * Get all registered components with their metadata.
     *
     * Returns components that implement DynamicallyCreatableComponent (if interface exists).
     * Each component includes: class, key, description, schema, example.
     *
     * @return array<string, array>
     */
    public static function all(): array
    {
        return collect(static::$components)
            ->filter(function ($class) {
                // Check if DynamicallyCreatableComponent interface exists
                if (! interface_exists('BernskioldMedia\\LaravelPpt\\Contracts\\DynamicallyCreatableComponent')) {
                    return true; // Return all if interface doesn't exist yet
                }

                return is_subclass_of($class, 'BernskioldMedia\\LaravelPpt\\Contracts\\DynamicallyCreatableComponent');
            })
            ->mapWithKeys(fn ($class) => [
                $class::key() => static::buildComponentDefinition($class),
            ])
            ->all();
    }

    /**
     * Build component definition array with metadata.
     *
     * @param  class-string  $class
     */
    protected static function buildComponentDefinition(string $class): array
    {
        $definition = ['class' => $class];

        if (method_exists($class, 'key')) {
            $definition['key'] = $class::key();
        }

        // Add schema info if available (from DynamicallyCreatableComponent interface)
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
     * Get a specific component definition by its key.
     *
     * @param  string  $componentKey  The component key (e.g., 'text-box', 'image')
     */
    public static function get(string $componentKey): ?array
    {
        return static::all()[$componentKey] ?? null;
    }

    /**
     * Check if a component exists by its key.
     *
     * @param  string  $componentKey  The component key (e.g., 'text-box')
     */
    public static function exists(string $componentKey): bool
    {
        return isset(static::all()[$componentKey]);
    }

    /**
     * Get the class name for a component by its key.
     *
     * @param  string  $componentKey  The component key (e.g., 'text-box')
     * @return class-string|null
     */
    public static function getClass(string $componentKey): ?string
    {
        $component = static::get($componentKey);

        return $component['class'] ?? null;
    }

    /**
     * Get available component keys.
     *
     * @return array<string> Array of component keys (e.g., ['text-box', 'image', 'shape', ...])
     */
    public static function names(): array
    {
        return array_keys(static::all());
    }

    /**
     * Register multiple components at once.
     *
     * This is typically called in a service provider's boot() method:
     *
     * Components::register([
     *     TextBox::class,
     *     Image::class,
     *     Shape::class,
     * ]);
     *
     * @param  array<class-string>  $components  Array of component class names
     */
    public static function register(array $components): void
    {
        static::$components = array_merge(
            static::$components,
            $components
        );
    }

    /**
     * Unregister one or more components by class name.
     *
     * Components::unregister([TextBox::class, CustomComponent::class]);
     *
     * @param  array<class-string>  $classes  Array of component class names to remove
     */
    public static function unregister(array $classes): void
    {
        static::$components = array_filter(
            static::$components,
            fn ($class) => ! in_array($class, $classes, true)
        );

        // Re-index the array to maintain sequential numeric keys
        static::$components = array_values(static::$components);
    }

    /**
     * Get all registered component classes (raw list without metadata).
     *
     * @return array<class-string>
     */
    public static function classes(): array
    {
        return static::$components;
    }

    /**
     * Clear all registered components (primarily for testing).
     */
    public static function clear(): void
    {
        static::$components = [];
    }
}
