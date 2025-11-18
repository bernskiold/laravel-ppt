<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBackgroundColor;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBorder;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithRotation;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithShape;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithUrl;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Shape\AutoShape;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

/**
 * @property AutoShape $shape
 *
 * @method static static make(BaseSlide $slide)
 */
class Shape extends Component
{
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
}
