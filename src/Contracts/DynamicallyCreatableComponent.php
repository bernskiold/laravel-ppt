<?php

namespace BernskioldMedia\LaravelPpt\Contracts;

use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

interface DynamicallyCreatableComponent
{
    /**
     * Get the JSON schema for this component's data requirements.
     *
     * The schema will be automatically merged with schemas from any traits
     * that define a dataSchema{TraitName}() method.
     *
     * @return array JSON Schema format array
     */
    public static function dataSchema(): array;

    /**
     * Get a human-readable description of this component.
     */
    public static function description(): string;

    /**
     * Get an example data structure for this component.
     *
     * This should be a valid data array that could be passed to ComponentFactory::create()
     */
    public static function exampleData(): array;

    /**
     * Get the registry key for this component.
     *
     * This is used to identify the component in the registry (e.g., 'text-box', 'image', 'shape')
     */
    public static function key(): string;

    /**
     * Create an instance of the component from a data array.
     *
     * @param  BaseSlide  $slide  The slide to render the component on
     * @param  array  $data  The data array containing all component parameters
     * @return static The component instance (not yet rendered)
     */
    public static function fromData(BaseSlide $slide, array $data): static;
}
