<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $slideTitle, string $chartTitle, ChartComponent $chart)
 */
class ChartTitles extends BaseSlide
{
    use WithSlideTitle;

    public function __construct(
        string $slideTitle,
        protected string $chartTitle,
        protected ChartComponent $chart,
    ) {
        $this->title($slideTitle);
    }

    protected function render(): void
    {
        $title = $this->renderTitle();

        ChartShape::make($this, $this->chart->slide($this)->get())
            ->height($this->presentation->height - $title->height - $this->verticalPadding * 2 - 20)
            ->width($this->presentation->width - $this->horizontalPadding * 2)
            ->centerHorizontally()
            ->y($title->height + $this->verticalPadding + 20)
            ->backgroundColor($this->chartBackgroundColor)
            ->title($this->chartTitle)
            ->render();

    }
}
