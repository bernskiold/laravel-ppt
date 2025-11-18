<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title, ChartComponent $chart)
 */
class ChartTitle extends BaseSlide implements DynamicallyCreatable
{
    use WithSlideTitle;

    public function __construct(
        string $title,
        protected ChartComponent $chart,
    ) {
        $this->title($title);
    }

    protected function render(): void
    {
        $title = $this->renderTitle();

        ChartShape::make($this, $this->chart->slide($this)->get())
            ->height($this->presentation->height - $title->height - $this->verticalPadding * 2 - 20)
            ->width($this->presentation->width - $this->horizontalPadding * 2)
            ->centerHorizontally()
            ->backgroundColor($this->chartBackgroundColor)
            ->y($title->height + $this->verticalPadding + 20)
            ->render();

    }

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'description' => 'The slide title',
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
            'required' => ['title', 'chartType', 'chartData'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title and a chart below it';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Q4 Performance',
            'chartType' => 'Bar',
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
