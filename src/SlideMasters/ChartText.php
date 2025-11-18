<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $text, ChartComponent $chart)
 */
class ChartText extends BaseSlide
{
    public function __construct(
        protected string $text,
        protected ChartComponent $chart,
    ) {}

    protected function render(): void
    {
        ChartShape::make($this, $this->chart->slide($this)->get())
            ->height($this->height + 1)
            ->width(660)
            ->backgroundColor($this->chartBackgroundColor)
            ->position(620, 0)
            ->render();

        TextBox::make($this, $this->text)
            ->paragraphStyle('body')
            ->alignLeft()
            ->alignMiddle()
            ->position($this->horizontalPadding, $this->verticalPadding)
            ->height(560)
            ->width(520)
            ->render();
    }
}
