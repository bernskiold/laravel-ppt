<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static self make(string $text)
 */
class Text extends BaseSlide
{
    public function __construct(
        protected string $text
    ) {}

    protected function render(): void
    {
        TextBox::make($this, $this->text)
            ->paragraphStyle('body')
            ->width($this->width * 0.66)
            ->centered()
            ->render();
    }
}
