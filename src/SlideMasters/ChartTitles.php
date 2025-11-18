<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $slideTitle, string $chartTitle, ChartComponent $chart)
 */
class ChartTitles extends BaseSlide implements DynamicallyCreatable
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

        $width = $this->chart->width ?? $this->presentation->width;
        $height = $this->chart->height ?? $this->presentation->height;

        ChartShape::make($this, $this->chart->slide($this)->get())
            ->height($height - $title->height - $this->verticalPadding * 2 - 20)
            ->width($width - $this->horizontalPadding * 2)
            ->centerHorizontally()
            ->y($title->height + $this->verticalPadding + 20)
            ->backgroundColor($this->chartBackgroundColor)
            ->title($this->chartTitle)
            ->render();

    }

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'slideTitle' => [
                    'type' => 'string',
                    'description' => 'The slide title',
                ],
                'chartTitle' => [
                    'type' => 'string',
                    'description' => 'The chart title',
                ],
                'chartType' => [
                    'type' => 'string',
                    'enum' => ['Bar', 'StackedBar', 'PercentageStackedBar', 'Column', 'StackedColumn', 'PercentageStackedColumn', 'Line', 'Radar', 'Scatter'],
                    'description' => 'Type of chart to render',
                ],
                'chartData' => [
                    'type' => 'object',
                    'description' => 'Chart data with series',
                    'properties' => [
                        'series' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'label' => ['type' => 'string'],
                                    'data' => ['type' => 'object'],
                                ],
                                'required' => ['label', 'data'],
                            ],
                        ],
                    ],
                    'required' => ['series'],
                ],
            ],
            'required' => ['slideTitle', 'chartTitle', 'chartType', 'chartData'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a slide title and a chart with its own title';
    }

    public static function exampleData(): array
    {
        return [
            'slideTitle' => 'Q4 Performance Overview',
            'chartTitle' => 'Revenue Growth',
            'chartType' => 'Column',
            'chartData' => [
                'series' => [
                    [
                        'label' => '2024',
                        'data' => ['Q1' => 100, 'Q2' => 120, 'Q3' => 135, 'Q4' => 150],
                    ],
                ],
            ],
        ];
    }
}
