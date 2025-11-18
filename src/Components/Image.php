<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\Slides\WithShape;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Shape\Drawing\File;
use PhpOffice\PhpPresentation\Style\Border;

/**
 * @method static static make(BaseSlide $slide, string $path)
 */
class Image extends Component
{
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
}
