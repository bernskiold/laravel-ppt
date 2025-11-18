<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\HasDataSchema;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBackgroundColor;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBorder;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithRotation;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithShape;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithUrl;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatableComponent;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Shape\AutoShape;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

/**
 * @property AutoShape $shape
 *
 * @method static static make(BaseSlide $slide)
 */
class Shape extends Component implements DynamicallyCreatableComponent
{
    use HasDataSchema;
    use WithBackgroundColor,
        WithBorder,
        WithRotation,
        WithShape,
        WithUrl;

    protected string $type = AutoShape::TYPE_RECTANGLE;

    protected function initialize(): static
    {
        $this->shape = new AutoShape;

        return $this;
    }

    /**
     * Set the type of the shape.
     * See the AutoShape class for available types as constants.
     */
    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the shape to be a circle.
     */
    public function round(): static
    {
        return $this->type(AutoShape::TYPE_OVAL);
    }

    /**
     * Set the shape to be a rounded rectangle.
     */
    public function rounded(): static
    {
        return $this->type(AutoShape::TYPE_ROUNDED_RECTANGLE);
    }

    public function render(): static
    {
        $this->shape
            ->setWidth($this->width)
            ->setHeight($this->height)
            ->setOffsetX($this->x)
            ->setOffsetY($this->y)
            ->setType($this->type);

        if ($this->backgroundColor) {
            $this->shape->setFill(
                (new Fill)->setFillType(Fill::FILL_SOLID)
                    ->setStartColor(new Color($this->backgroundColor))
                    ->setEndColor(new Color($this->backgroundColor))
            );
        }

        if ($this->url) {
            $this->shape->setHyperlink($this->getLinkAsHyperlink());
        }

        if ($this->borderColor) {
            $this->shape->setOutline($this->getBorderAsOutline());
        }

        $this->shape->setRotation($this->rotation);

        $this->slide->raw()->addShape($this->shape);

        return $this;
    }

    public static function key(): string
    {
        return 'shape';
    }

    public static function description(): string
    {
        return 'A geometric shape component (rectangle, circle, rounded rectangle, etc.)';
    }

    public static function dataSchema(): array
    {
        return static::buildDataSchema([
            'properties' => [
                'shape' => [
                    'type' => 'string',
                    'description' => 'Shape type: rectangle, round (circle), rounded (rounded rectangle)',
                    'enum' => ['rectangle', 'round', 'rounded'],
                ],
            ],
            'required' => [],
        ]);
    }

    public static function exampleData(): array
    {
        return [
            'shape' => 'round',
            'backgroundColor' => '3498db',
            'x' => 100,
            'y' => 100,
            'width' => 200,
            'height' => 200,
        ];
    }

    public static function fromData(BaseSlide $slide, array $data): static
    {
        $component = static::make($slide);

        // Apply shape type
        if (isset($data['shape'])) {
            match ($data['shape']) {
                'round' => $component->round(),
                'rounded' => $component->rounded(),
                default => $component->type(AutoShape::TYPE_RECTANGLE),
            };
        }

        // Apply position
        if (isset($data['x']) && isset($data['y'])) {
            $component->position($data['x'], $data['y']);
        } elseif (isset($data['x'])) {
            $component->x($data['x']);
        } elseif (isset($data['y'])) {
            $component->y($data['y']);
        }

        // Apply size
        if (isset($data['width'])) {
            $component->width($data['width']);
        }
        if (isset($data['height'])) {
            $component->height($data['height']);
        }

        // Apply background color
        if (isset($data['backgroundColor'])) {
            $component->backgroundColor($data['backgroundColor']);
        }

        // Apply rotation
        if (isset($data['rotation'])) {
            $component->rotate($data['rotation']);
        }

        // Apply URL
        if (isset($data['url'])) {
            $component->url($data['url']);
        }

        return $component;
    }
}
