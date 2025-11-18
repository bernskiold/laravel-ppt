<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $text, ChartComponent $chart)
 */
class ChartText extends BaseSlide implements DynamicallyCreatable
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

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'text' => [
                    'type' => 'string',
                    'description' => 'The text content',
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
            'required' => ['text', 'chartType', 'chartData'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with text on the left and a chart on the right';
    }

    public static function exampleData(): array
    {
        return [
            'text' => 'Our quarterly performance shows consistent growth across all metrics.',
            'chartType' => 'Line',
            'chartData' => [
                'series' => [
                    [
                        'label' => 'Revenue',
                        'data' => ['Q1' => 100, 'Q2' => 120, 'Q3' => 135, 'Q4' => 150],
                    ],
                ],
            ],
        ];
    }
}
