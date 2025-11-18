<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Concerns\Slides\HasBoxes;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title = '', array $boxes = [])
 */
class ThreeUp extends BaseSlide implements DynamicallyCreatable
{
    use HasBoxes,
        WithSlideTitle;

    public function __construct(
        string $title = '',
        array $boxes = [],
    ) {
        $this->slideTitle = $title;
        $this->boxes = $boxes;
    }

    protected function render(): void
    {
        if (! empty($this->slideTitle)) {
            $this->renderTitle();
        }

        $this->makeBoxes(1, 1);
        $this->makeBoxes(2, 2);
        $this->makeBoxes(3, 3);
    }

    protected function makeBoxes(int $index, int $column = 1): void
    {
        if (! isset($this->boxes[$index - 1])) {
            return;
        }

        $columnGap = 30;
        $boxWidth = (int) (($this->presentation->width - (2 * $this->horizontalPadding) - ($columnGap * 2)) / 3);
        $yOffset = 150;
        $xOffset = $this->horizontalPadding + (($column - 1) * ($boxWidth + $columnGap));

        $title = TextBox::make($this, $this->boxes[$index - 1]['title'])
            ->paragraphStyle('nUpGridTitle')
            ->width($boxWidth)
            ->position($xOffset, $yOffset)
            ->alignLeft()
            ->alignBottom()
            ->render();

        TextBox::make($this, $this->boxes[$index - 1]['description'])
            ->paragraphStyle('nUpGridBody')
            ->alignLeft()
            ->alignTop()
            ->width($boxWidth)
            ->position($xOffset, $yOffset + $title->height + 5)
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
                'boxes' => [
                    'type' => 'array',
                    'description' => 'Array of box data (3 boxes in a row)',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                        ],
                        'required' => ['title', 'description'],
                    ],
                    'minItems' => 3,
                    'maxItems' => 3,
                ],
            ],
            'required' => ['title', 'boxes'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title and three content boxes in a single row';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Our Services',
            'boxes' => [
                [
                    'title' => 'Consulting',
                    'description' => 'Expert guidance for your business transformation.',
                ],
                [
                    'title' => 'Development',
                    'description' => 'Custom solutions built to your specifications.',
                ],
                [
                    'title' => 'Support',
                    'description' => '24/7 dedicated customer success team.',
                ],
            ],
        ];
    }
}
