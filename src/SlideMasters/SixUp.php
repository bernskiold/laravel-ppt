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
class SixUp extends BaseSlide implements DynamicallyCreatable
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
        $this->renderTitle();

        $this->makeBoxes(1, 1, 1);
        $this->makeBoxes(2, 2, 1);
        $this->makeBoxes(3, 3, 1);
        $this->makeBoxes(4, 1, 2);
        $this->makeBoxes(5, 2, 2);
        $this->makeBoxes(6, 3, 2);
    }

    protected function makeBoxes(int $index, int $column = 1, int $row = 1): void
    {
        if (! isset($this->boxes[$index - 1])) {
            return;
        }

        $boxWidth = 350;
        $boxHeight = 300;
        $yOffset = $row === 1 ? 100 : $boxHeight + 40;
        $xOffset = $column === 1 ? 40 : (($boxWidth * ($column - 1)) + (80 * ($column - 1)));

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
                    'description' => 'Array of box data (6 boxes in 3x2 grid)',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                        ],
                        'required' => ['title', 'description'],
                    ],
                    'minItems' => 6,
                    'maxItems' => 6,
                ],
            ],
            'required' => ['title', 'boxes'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title and six content boxes in a 3x2 grid';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Our Services',
            'boxes' => [
                [
                    'title' => 'Consulting',
                    'description' => 'Expert guidance for your business.',
                ],
                [
                    'title' => 'Development',
                    'description' => 'Custom software solutions.',
                ],
                [
                    'title' => 'Design',
                    'description' => 'Beautiful user experiences.',
                ],
                [
                    'title' => 'Testing',
                    'description' => 'Quality assurance and QA.',
                ],
                [
                    'title' => 'Support',
                    'description' => '24/7 customer assistance.',
                ],
                [
                    'title' => 'Training',
                    'description' => 'Empowering your team.',
                ],
            ],
        ];
    }
}
