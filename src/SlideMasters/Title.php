<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title = '')
 */
class Title extends BaseSlide implements DynamicallyCreatable
{
    public function __construct(
        protected string $title = ''
    ) {}

    protected function render(): void
    {
        TextBox::make($this, $this->title)
            ->paragraphStyle('sectionTitle')
            ->width($this->presentation->width - $this->horizontalPadding * 2)
            ->lines(1)
            ->position($this->horizontalPadding, $this->verticalPadding)
            ->centered()
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
            ],
            'required' => ['title'],
        ];
    }

    public static function description(): string
    {
        return 'A simple slide with a centered title';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Welcome to Our Presentation',
        ];
    }
}
