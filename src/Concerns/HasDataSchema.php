<?php

namespace BernskioldMedia\LaravelPpt\Concerns;

use ReflectionClass;

/**
 * Provides automatic trait-based schema merging for components.
 *
 * Similar to Laravel's trait initialization pattern (initializeTraitName),
 * this trait looks for dataSchema{TraitName}() methods in all used traits
 * and automatically merges them into the component's data schema.
 *
 * Example:
 * - Component uses WithPosition trait
 * - WithPosition defines dataSchemaWithPosition() method
 * - This trait automatically finds and merges that schema
 */
trait HasDataSchema
{
    /**
     * Get the complete data schema by merging the component's schema
     * with schemas from all traits that define dataSchema methods.
     */
    protected static function buildDataSchema(array $componentSchema = []): array
    {
        $mergedProperties = $componentSchema['properties'] ?? [];
        $mergedRequired = $componentSchema['required'] ?? [];

        // Get all trait schemas and merge them
        foreach (static::getTraitSchemas() as $traitSchema) {
            $mergedProperties = array_merge(
                $mergedProperties,
                $traitSchema['properties'] ?? []
            );

            $mergedRequired = array_merge(
                $mergedRequired,
                $traitSchema['required'] ?? []
            );
        }

        return [
            'type' => 'object',
            'properties' => $mergedProperties,
            'required' => array_unique($mergedRequired),
        ];
    }

    /**
     * Get schemas from all traits that define dataSchema methods.
     *
     * Looks for methods named dataSchema{TraitName}() in all traits used by this class.
     *
     * @return array Array of schema arrays from traits
     */
    protected static function getTraitSchemas(): array
    {
        $schemas = [];

        foreach (static::getTraitsWithSchemas() as $trait) {
            $method = 'dataSchema'.class_basename($trait);

            if (method_exists(static::class, $method)) {
                $schemas[] = static::$method();
            }
        }

        return $schemas;
    }

    /**
     * Get all traits used by this class recursively.
     *
     * @return array Array of trait names
     */
    protected static function getTraitsWithSchemas(): array
    {
        $traits = [];

        $class = static::class;

        do {
            $traits = array_merge(class_uses($class) ?: [], $traits);
        } while ($class = get_parent_class($class));

        // Also check traits used by traits (recursive)
        foreach (array_keys($traits) as $trait) {
            $traits = array_merge(class_uses($trait) ?: [], $traits);
        }

        return array_unique($traits);
    }

    /**
     * Extract default values from trait properties for the example data.
     *
     * This is a helper method that can be used to build example data
     * by inspecting the default values of public properties.
     */
    protected static function getDefaultValuesFromTraits(): array
    {
        $defaults = [];
        $reflection = new ReflectionClass(static::class);

        foreach ($reflection->getProperties() as $property) {
            if ($property->isPublic() && ! $property->isStatic()) {
                $property->setAccessible(true);
                $defaultValue = $property->getDefaultValue();

                if ($defaultValue !== null) {
                    $defaults[$property->getName()] = $defaultValue;
                }
            }
        }

        return $defaults;
    }
}
