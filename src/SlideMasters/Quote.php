<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $quote = '', string $attribution = '')
 */
class Quote extends BaseSlide implements DynamicallyCreatable
{
    public function __construct(
        protected string $quote = '',
        protected string $attribution = '',
    ) {}

    protected function render(): void
    {
        if (empty($this->quote)) {
            return;
        }

        $quoteWidth = $this->presentation->width - (2 * $this->horizontalPadding) - 200;
        $quoteXOffset = $this->horizontalPadding + 100;

        // Add opening quotation mark styling to the quote
        $formattedQuote = '"'.$this->quote.'"';

        // Render the quote centered vertically
        $quoteBox = TextBox::make($this, $formattedQuote)
            ->size(32)
            ->bold()
            ->width($quoteWidth)
            ->position($quoteXOffset, ($this->presentation->height / 2) - 100)
            ->alignCenter()
            ->alignMiddle()
            ->lines(6)
            ->render();

        // Render the attribution below the quote if provided
        if (! empty($this->attribution)) {
            TextBox::make($this, '— '.$this->attribution)
                ->size(20)
                ->width($quoteWidth)
                ->position($quoteXOffset, ($this->presentation->height / 2) + 50)
                ->alignRight()
                ->alignTop()
                ->render();
        }
    }

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'quote' => [
                    'type' => 'string',
                    'description' => 'The quote text to display',
                ],
                'attribution' => [
                    'type' => 'string',
                    'description' => 'Attribution for the quote (author, source, etc.)',
                ],
            ],
            'required' => ['quote'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a large pull quote and optional attribution';
    }

    public static function exampleData(): array
    {
        return [
            'quote' => 'The best way to predict the future is to invent it.',
            'attribution' => 'Alan Kay',
        ];
    }
}
