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
class TwoUp extends BaseSlide implements DynamicallyCreatable
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
    }

    protected function makeBoxes(int $index, int $column = 1): void
    {
        if (! isset($this->boxes[$index - 1])) {
            return;
        }

        $boxWidth = 570;
        $yOffset = 150;
        $xOffset = $column === 1 ? 40 : $boxWidth + 80;

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
            ->height(400)
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
                    'description' => 'Array of box data (2 boxes)',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                        ],
                        'required' => ['title', 'description'],
                    ],
                    'minItems' => 2,
                    'maxItems' => 2,
                ],
            ],
            'required' => ['title', 'boxes'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title and two content boxes side by side';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Our Approach',
            'boxes' => [
                [
                    'title' => 'Strategy',
                    'description' => 'Focus on data-driven decisions and customer feedback.',
                ],
                [
                    'title' => 'Execution',
                    'description' => 'Agile methodology with bi-weekly sprints.',
                ],
            ],
        ];
    }
}
