<?php

namespace BernskioldMedia\LaravelPpt\Contracts;

interface DynamicallyCreatable
{
    /**
     * Get the JSON schema for this slide master's data requirements.
     *
     * Property names in the schema MUST match constructor parameter names exactly.
     * This enables automatic mapping of data to constructor parameters.
     *
     * @return array JSON Schema format array
     */
    public static function dataSchema(): array;

    /**
     * Get a human-readable description of this slide master.
     *
     * @return string
     */
    public static function description(): string;

    /**
     * Get an example data structure for this slide master.
     *
     * This should be a valid data array that could be passed to SlideFactory::create()
     *
     * @return array
     */
    public static function exampleData(): array;
}
