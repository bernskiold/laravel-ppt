<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Concerns\Slides\WithCustomContents;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use BernskioldMedia\LaravelPpt\Support\ComponentFactory;

/**
 * @method static static make(callable|array $components)
 */
class Blank extends BaseSlide implements DynamicallyCreatable
{
    use WithCustomContents;

    protected array $componentsData = [];

    /**
     * Create a blank slide.
     *
     * @param  callable|array  $components  Either a callback for custom content or an array of component definitions
     */
    public function __construct(callable|array $components = [])
    {
        if (is_callable($components)) {
            // Backward compatibility: use callback pattern
            $this->contents = $components;
        } else {
            // New pattern: use component definitions
            $this->componentsData = $components;
        }
    }

    protected function render(): void
    {
        // Render callback-based content if provided
        if ($this->contents) {
            $this->renderContents();
        }

        // Render component-based content if provided
        if (! empty($this->componentsData)) {
            $components = ComponentFactory::createMany($this, $this->componentsData);

            foreach ($components as $component) {
                $component->render();
            }
        }
    }

    public static function key(): string
    {
        return 'blank';
    }

    public static function label(): string
    {
        return 'Blank';
    }

    public static function description(): string
    {
        return 'A completely blank slide with custom components';
    }

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'components' => [
                    'type' => 'array',
                    'description' => 'Array of component definitions to render on the slide',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'type' => [
                                'type' => 'string',
                                'description' => 'Component type (e.g., "text-box", "image", "shape")',
                            ],
                            'data' => [
                                'type' => 'object',
                                'description' => 'Component-specific data',
                            ],
                        ],
                        'required' => ['type', 'data'],
                    ],
                ],
            ],
            'required' => ['components'],
        ];
    }

    public static function exampleData(): array
    {
        return [
            'components' => [
                [
                    'type' => 'text-box',
                    'data' => [
                        'text' => 'Custom Content',
                        'x' => 100,
                        'y' => 100,
                        'fontSize' => 32,
                        'bold' => true,
                    ],
                ],
                [
                    'type' => 'shape',
                    'data' => [
                        'shape' => 'round',
                        'backgroundColor' => '3498db',
                        'x' => 200,
                        'y' => 200,
                        'width' => 200,
                        'height' => 200,
                    ],
                ],
            ],
        ];
    }
}
