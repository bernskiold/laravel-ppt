<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title = '')
 */
class Title extends BaseSlide
{
    public function __construct(
        protected string $title = ''
    ) {}

    protected function render(): void
    {
        TextBox::make($this, $this->title)
            ->paragraphStyle('sectionTitle')
            ->width($this->presentation->width - $this->horizontalPadding * 2)
            ->lines(1)
            ->position($this->horizontalPadding, $this->verticalPadding)
            ->centered()
            ->render();
    }
}
