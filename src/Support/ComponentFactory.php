<?php

namespace BernskioldMedia\LaravelPpt\Support;

use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatableComponent;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use BernskioldMedia\LaravelPpt\Registries\Components;
use InvalidArgumentException;

class ComponentFactory
{
    /**
     * Create a component instance from a component type and data array.
     *
     * @param  BaseSlide  $slide  The slide to render the component on
     * @param  string  $componentType  Component key (e.g., 'text-box') or fully qualified class name
     * @param  array  $data  Data array containing all component parameters
     * @return DynamicallyCreatableComponent The component instance (not yet rendered)
     *
     * @throws InvalidArgumentException If component type is not found or invalid
     */
    public static function create(BaseSlide $slide, string $componentType, array $data): DynamicallyCreatableComponent
    {
        $componentClass = static::resolveComponentClass($componentType);

        if (! class_exists($componentClass)) {
            throw new InvalidArgumentException("Component class [{$componentClass}] does not exist.");
        }

        if (! is_subclass_of($componentClass, DynamicallyCreatableComponent::class)) {
            throw new InvalidArgumentException(
                "Component class [{$componentClass}] must implement DynamicallyCreatableComponent interface."
            );
        }

        // Use the component's fromData factory method
        return $componentClass::fromData($slide, $data);
    }

    /**
     * Create multiple components from an array of component definitions.
     *
     * Each component definition should have 'type' and 'data' keys.
     *
     * Example:
     * [
     *     ['type' => 'text-box', 'data' => ['text' => 'Hello', 'x' => 100, 'y' => 100]],
     *     ['type' => 'image', 'data' => ['path' => '/images/logo.png', 'x' => 200, 'y' => 200]],
     * ]
     *
     * @param  BaseSlide  $slide  The slide to render components on
     * @param  array  $components  Array of component definitions
     * @return array Array of component instances (not yet rendered)
     */
    public static function createMany(BaseSlide $slide, array $components): array
    {
        return array_map(
            fn (array $componentDef) => static::create(
                $slide,
                $componentDef['type'] ?? throw new InvalidArgumentException('Component definition must have a "type" key.'),
                $componentDef['data'] ?? []
            ),
            $components
        );
    }

    /**
     * Resolve a component type (key or class name) to a fully qualified class name.
     *
     * @param  string  $componentType  Component key (e.g., 'text-box') or fully qualified class name
     * @return class-string
     *
     * @throws InvalidArgumentException If component type cannot be resolved
     */
    protected static function resolveComponentClass(string $componentType): string
    {
        // If it's already a fully qualified class name (contains backslashes), use it directly
        if (str_contains($componentType, '\\')) {
            return $componentType;
        }

        // Try to resolve from registry
        $componentClass = Components::getClass($componentType);

        if (! $componentClass) {
            throw new InvalidArgumentException(
                "Component type [{$componentType}] is not registered. ".
                'Available components: '.implode(', ', Components::names())
            );
        }

        return $componentClass;
    }
}
