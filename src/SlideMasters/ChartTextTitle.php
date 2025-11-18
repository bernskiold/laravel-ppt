<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title = '', string $text = '', ChartComponent $chart = null)
 */
class ChartTextTitle extends BaseSlide implements DynamicallyCreatable
{
    use WithSlideTitle;

    public function __construct(
        string $title = '',
        protected string $text = '',
        protected ?ChartComponent $chart = null,
    ) {
        $this->slideTitle = $title;
    }

    protected function render(): void
    {
        // Render title and calculate offset
        if (! empty($this->slideTitle)) {
            $titleBox = $this->renderTitle();
            $yOffset = $titleBox->height + 75;
        } else {
            $yOffset = $this->verticalPadding;
        }

        // Calculate available dimensions
        $availableWidth = $this->presentation->width - (2 * $this->horizontalPadding);
        $availableHeight = $this->presentation->height - $yOffset - $this->verticalPadding;

        // Calculate text and chart widths (approximately 40/60 split with gap)
        $gap = 40;
        $textWidth = (int) ($availableWidth * 0.4);
        $chartWidth = $availableWidth - $textWidth - $gap;
        $chartXPosition = $this->horizontalPadding + $textWidth + $gap;

        // Render chart on the right
        if ($this->chart) {
            ChartShape::make($this, $this->chart->slide($this)->get())
                ->height($availableHeight)
                ->width($chartWidth)
                ->backgroundColor($this->chartBackgroundColor)
                ->position($chartXPosition, $yOffset)
                ->render();
        }

        // Render text on the left
        if (! empty($this->text)) {
            TextBox::make($this, $this->text)
                ->paragraphStyle('body')
                ->alignLeft()
                ->alignMiddle()
                ->position($this->horizontalPadding, $yOffset)
                ->height($availableHeight)
                ->width($textWidth)
                ->render();
        }
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
                'text' => [
                    'type' => 'string',
                    'description' => 'The text content for the left side',
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
            'required' => ['title', 'text', 'chartType', 'chartData'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title at the top, text on the left, and a chart on the right';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'This is a chart that we want to describe',
            'text' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec odio. Quisque volutpat mattis eros. Nullam malesuada erat ut turpis. Suspendisse urna nibh, viverra non, semper suscipit, posuere a, pede.',
            'chartType' => 'Column',
            'chartData' => [
                'series' => [
                    [
                        'label' => 'Series 1',
                        'data' => ['Category 1' => 4, 'Category 2' => 2, 'Category 3' => 3, 'Category 4' => 5],
                    ],
                    [
                        'label' => 'Series 2',
                        'data' => ['Category 1' => 2, 'Category 2' => 5, 'Category 3' => 2, 'Category 4' => 3],
                    ],
                    [
                        'label' => 'Series 3',
                        'data' => ['Category 1' => 2, 'Category 2' => 2, 'Category 3' => 3, 'Category 4' => 5],
                    ],
                ],
            ],
        ];
    }
}
