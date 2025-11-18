<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title, string $subtitle)
 */
class TitleSubtitle extends BaseSlide implements DynamicallyCreatable
{
    public function __construct(
        public string $title,
        public string $subtitle,
    ) {}

    protected function render(): void
    {
        TextBox::make($this, $this->title)
            ->paragraphStyle('sectionTitle')
            ->alignBottom()
            ->lines(1)
            ->y(($this->presentation->height / 2) - ($this->presentation->branding->paragraphStyleValue('sectionTitle', 'size') + 10))
            ->centerHorizontally()
            ->render();

        TextBox::make($this, $this->subtitle)
            ->paragraphStyle('sectionSubtitle')
            ->alignTop()
            ->lines(2)
            ->y(($this->presentation->height / 2) + 10)
            ->centerHorizontally()
            ->render();
    }

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'description' => 'The main title',
                ],
                'subtitle' => [
                    'type' => 'string',
                    'description' => 'The subtitle',
                ],
            ],
            'required' => ['title', 'subtitle'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a centered title and subtitle';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Annual Report',
            'subtitle' => 'Fiscal Year 2024',
        ];
    }
}
