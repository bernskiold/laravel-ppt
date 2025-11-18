<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title, string $subtitle)
 */
class TitleSubtitle extends BaseSlide
{
    public function __construct(
        public string $title,
        public string $subtitle,
    ) {}

    protected function render(): void
    {
        TextBox::make($this, $this->title)
            ->paragraphStyle('sectionTitle')
            ->alignBottom()
            ->lines(1)
            ->y(($this->presentation->height / 2) - ($this->presentation->branding->paragraphStyleValue('sectionTitle', 'size') + 10))
            ->centerHorizontally()
            ->render();

        TextBox::make($this, $this->subtitle)
            ->paragraphStyle('sectionSubtitle')
            ->alignTop()
            ->lines(2)
            ->y(($this->presentation->height / 2) + 10)
            ->centerHorizontally()
            ->render();
    }
}
