<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title = '', string $leftColumn = '', string $rightColumn = '')
 */
class TwoColumn extends BaseSlide implements DynamicallyCreatable
{
    use WithSlideTitle;

    public function __construct(
        string $title = '',
        protected string $leftColumn = '',
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

        $columnGap = 40;
        $columnWidth = (int) (($this->presentation->width - (2 * $this->horizontalPadding) - $columnGap) / 2);
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

        // Right column
        if (! empty($this->rightColumn)) {
            TextBox::make($this, $this->rightColumn)
                ->paragraphStyle('bodyText')
                ->width($columnWidth)
                ->height($columnHeight)
                ->position($this->horizontalPadding + $columnWidth + $columnGap, $yOffset)
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
                'rightColumn' => [
                    'type' => 'string',
                    'description' => 'Text content for the right column',
                ],
            ],
            'required' => ['title', 'leftColumn', 'rightColumn'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title and text in two columns (newspaper layout)';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Company Overview',
            'leftColumn' => 'Founded in 2010, our company has grown from a small startup to a leading provider of innovative solutions. We pride ourselves on delivering exceptional value to our customers through cutting-edge technology and dedicated service.',
            'rightColumn' => 'Our team of experts brings decades of combined experience across multiple industries. We focus on creating sustainable, long-term partnerships with our clients and helping them achieve their business goals through strategic innovation.',
        ];
    }
}
