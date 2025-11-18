<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\HasDataSchema;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithShape;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatableComponent;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Shape\Drawing\File;
use PhpOffice\PhpPresentation\Style\Border;

/**
 * @method static static make(BaseSlide $slide, string $path)
 */
class Image extends Component implements DynamicallyCreatableComponent
{
    use HasDataSchema;
    use WithShape;

    public function __construct(
        protected string $path
    ) {
        $this->shape = (new File)
            ->setPath($path)
            ->setName(str()->random());
    }

    public function render(): static
    {
        $this->shape->setOffsetX($this->x)
            ->setOffsetY($this->y);

        $this->shape->getBorder()->setLineStyle(Border::LINE_NONE);

        if ($this->width) {
            $this->shape->setWidth($this->width);
        }

        if ($this->height) {
            $this->shape->setHeight($this->height);
        }

        $this->slide->raw()->addShape($this->shape);

        return $this;
    }

    /**
     * Get the registry key for this component.
     */
    public static function key(): string
    {
        return 'image';
    }

    /**
     * Get a human-readable description of this component.
     */
    public static function description(): string
    {
        return 'An image component for displaying pictures and graphics';
    }

    /**
     * Get the JSON schema for this component's data requirements.
     */
    public static function dataSchema(): array
    {
        return static::buildDataSchema([
            'properties' => [
                'path' => [
                    'type' => 'string',
                    'description' => 'Path to the image file (absolute or relative)',
                ],
            ],
            'required' => ['path'],
        ]);
    }

    /**
     * Get an example data structure for this component.
     */
    public static function exampleData(): array
    {
        return [
            'path' => '/images/logo.png',
            'x' => 100,
            'y' => 100,
            'width' => 300,
            'height' => 200,
        ];
    }

    /**
     * Create an instance of the component from a data array.
     */
    public static function fromData(BaseSlide $slide, array $data): static
    {
        $path = $data['path'];
        $component = static::make($slide, $path);

        // Apply position if provided
        if (isset($data['x']) && isset($data['y'])) {
            $component->position($data['x'], $data['y']);
        } elseif (isset($data['x'])) {
            $component->x($data['x']);
        } elseif (isset($data['y'])) {
            $component->y($data['y']);
        }

        // Apply size if provided
        if (isset($data['width'])) {
            $component->width($data['width']);
        }
        if (isset($data['height'])) {
            $component->height($data['height']);
        }

        return $component;
    }
}
