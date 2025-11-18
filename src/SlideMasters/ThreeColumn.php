<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title = '', string $leftColumn = '', string $middleColumn = '', string $rightColumn = '')
 */
class ThreeColumn extends BaseSlide implements DynamicallyCreatable
{
    use WithSlideTitle;

    public function __construct(
        string $title = '',
        protected string $leftColumn = '',
        protected string $middleColumn = '',
        protected string $rightColumn = '',
    ) {
        $this->slideTitle = $title;
    }

    protected function render(): void
    {
        if (! empty($this->slideTitle)) {
            $titleBox = $this->renderTitle();
            $yOffset = $titleBox->height + 75;
        } else {
            $yOffset = 75;
        }

        $columnGap = 30;
        $totalGaps = $columnGap * 2;
        $columnWidth = (int) (($this->presentation->width - (2 * $this->horizontalPadding) - $totalGaps) / 3);
        $columnHeight = $this->presentation->height - $yOffset - 75;

        // Left column
        if (! empty($this->leftColumn)) {
            TextBox::make($this, $this->leftColumn)
                ->paragraphStyle('bodyText')
                ->width($columnWidth)
                ->height($columnHeight)
                ->position($this->horizontalPadding, $yOffset)
                ->alignLeft()
                ->alignTop()
                ->render();
        }

        // Middle column
        if (! empty($this->middleColumn)) {
            TextBox::make($this, $this->middleColumn)
                ->paragraphStyle('bodyText')
                ->width($columnWidth)
                ->height($columnHeight)
                ->position($this->horizontalPadding + $columnWidth + $columnGap, $yOffset)
                ->alignLeft()
                ->alignTop()
                ->render();
        }

        // Right column
        if (! empty($this->rightColumn)) {
            TextBox::make($this, $this->rightColumn)
                ->paragraphStyle('bodyText')
                ->width($columnWidth)
                ->height($columnHeight)
                ->position($this->horizontalPadding + ($columnWidth * 2) + ($columnGap * 2), $yOffset)
                ->alignLeft()
                ->alignTop()
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
                'leftColumn' => [
                    'type' => 'string',
                    'description' => 'Text content for the left column',
                ],
                'middleColumn' => [
                    'type' => 'string',
                    'description' => 'Text content for the middle column',
                ],
                'rightColumn' => [
                    'type' => 'string',
                    'description' => 'Text content for the right column',
                ],
            ],
            'required' => ['title', 'leftColumn', 'middleColumn', 'rightColumn'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title and text in three equal columns';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Our Three Pillars',
            'leftColumn' => 'Innovation: We continuously push boundaries and explore new technologies to deliver cutting-edge solutions.',
            'middleColumn' => 'Quality: Every product and service meets our rigorous standards for excellence and reliability.',
            'rightColumn' => 'Service: Our dedicated team provides exceptional support to ensure customer success.',
        ];
    }
}
