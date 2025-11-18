<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(ChartComponent $chart)
 */
class ChartSquare extends BaseSlide
{
    public function __construct(
        protected ChartComponent $chart
    ) {}

    protected function render(): void
    {
        ChartShape::make($this, $this->chart->slide($this)->get())
            ->width(640) // Slightly bigger for axes.
            ->height(600)
            ->backgroundColor($this->chartBackgroundColor)
            ->centered()
            ->render();
    }
}
