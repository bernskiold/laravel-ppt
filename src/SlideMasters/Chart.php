<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(ChartComponent $chart)
 */
class Chart extends BaseSlide implements DynamicallyCreatable
{
    public function __construct(
        protected ChartComponent $chart
    ) {}

    protected function render(): void
    {
        ChartShape::make($this, $this->chart->slide($this)->get())
            ->centered()
            ->backgroundColor($this->chartBackgroundColor)
            ->render();
    }

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
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
            'required' => ['chartType', 'chartData'],
        ];
    }

    public static function description(): string
    {
        return 'A full-screen chart slide';
    }

    public static function exampleData(): array
    {
        return [
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
