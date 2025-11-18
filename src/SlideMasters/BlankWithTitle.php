<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Concerns\Slides\WithCustomContents;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use BernskioldMedia\LaravelPpt\Support\ComponentFactory;

/**
 * @method static static make(string $title, callable|array|null $components = null)
 */
class BlankWithTitle extends BaseSlide implements DynamicallyCreatable
{
    use WithCustomContents,
        WithSlideTitle;

    protected array $componentsData = [];

    /**
     * Create a blank slide with a title.
     *
     * @param  string  $title  The slide title
     * @param  callable|array|null  $components  Either a callback for custom content or an array of component definitions
     */
    public function __construct(
        string $title,
        callable|array|null $components = null
    ) {
        $this->title($title);

        if (is_callable($components)) {
            // Backward compatibility: use callback pattern
            $this->contents = $components;
        } elseif (is_array($components)) {
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

        // Always render the title
        $this->renderTitle();
    }

    public static function key(): string
    {
        return 'blank-with-title';
    }

    public static function label(): string
    {
        return 'Blank with Title';
    }

    public static function description(): string
    {
        return 'A blank slide with a title and custom components';
    }

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'description' => 'The slide title',
                ],
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
            'required' => ['title', 'components'],
        ];
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Custom Dashboard',
            'components' => [
                [
                    'type' => 'text-box',
                    'data' => [
                        'text' => 'Revenue Overview',
                        'x' => 100,
                        'y' => 150,
                        'fontSize' => 24,
                    ],
                ],
                [
                    'type' => 'shape',
                    'data' => [
                        'shape' => 'rounded',
                        'backgroundColor' => 'e74c3c',
                        'x' => 100,
                        'y' => 250,
                        'width' => 300,
                        'height' => 200,
                    ],
                ],
                [
                    'type' => 'image',
                    'data' => [
                        'path' => '/images/chart.png',
                        'x' => 500,
                        'y' => 250,
                        'width' => 600,
                        'height' => 350,
                    ],
                ],
            ],
        ];
    }
}
