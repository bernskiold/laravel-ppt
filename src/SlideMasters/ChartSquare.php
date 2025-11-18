<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(ChartComponent $chart)
 */
class ChartSquare extends BaseSlide implements DynamicallyCreatable
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
        return 'A centered square chart slide (640x600)';
    }

    public static function exampleData(): array
    {
        return [
            'chartType' => 'Line',
            'chartData' => [
                'series' => [
                    [
                        'label' => '2024',
                        'data' => ['Jan' => 100, 'Feb' => 120, 'Mar' => 135, 'Apr' => 150],
                    ],
                ],
            ],
        ];
    }
}
