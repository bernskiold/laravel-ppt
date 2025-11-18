<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static self make(string $text)
 */
class Text extends BaseSlide implements DynamicallyCreatable
{
    public function __construct(
        protected string $text
    ) {}

    protected function render(): void
    {
        TextBox::make($this, $this->text)
            ->paragraphStyle('body')
            ->width($this->width * 0.66)
            ->centered()
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
            ],
            'required' => ['text'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with centered text content';
    }

    public static function exampleData(): array
    {
        return [
            'text' => 'This is the main content of the slide.',
        ];
    }
}
