<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;

/**
 * @method static static make(string $title = '', array $items = [])
 */
class Agenda extends BaseSlide implements DynamicallyCreatable
{
    use WithSlideTitle;

    public function __construct(
        string $title = '',
        protected array $items = [],
    ) {
        $this->slideTitle = $title;
    }

    public function item(string $text): self
    {
        $this->items[] = $text;

        return $this;
    }

    protected function render(): void
    {
        // Render the title rotated 90 degrees on the left side
        if (! empty($this->slideTitle)) {
            TextBox::make($this, $this->slideTitle)
                ->bold()
                ->rotate(270)
                ->width($this->presentation->height - 2*$this->verticalPadding)
                ->height(200)
                ->centerVertically()
                ->x($this->verticalPadding)
                ->size(100)
                ->uppercase()
                ->render();
        }

        if (empty($this->items)) {
            return;
        }

        // Content area starts from the left with padding
        $yOffset = 75;
        $xOffset = $this->horizontalPadding + 150; // Extra padding for rotated title
        $contentWidth = $this->presentation->width - $xOffset - $this->horizontalPadding;

        $box = null;

        foreach ($this->items as $item) {
            if (! $box) {
                $box = TextBox::make($this, $item)
                    ->paragraphStyle('bodyText')
                    ->size(24)
                    ->height($this->presentation->height - $yOffset - 75)
                    ->width($contentWidth)
                    ->position($xOffset, $yOffset)
                    ->alignLeft()
                    ->alignTop()
                    ->render()
                    ->shape;
            } else {
                $box->createParagraph()
                    ->createTextRun($item)
                    ->getFont()
                    ->setSize(24)
                    ->setColor(new Color($this->textColor))
                    ->setBold(false)
                    ->setName($this->presentation->branding->baseFont())
                    ->setCharacterSpacing(0);
            }

            $box->getActiveParagraph()
                ->getBulletStyle()
                ->setBulletType(Bullet::TYPE_NUMERIC)
                ->setBulletColor(new Color($this->textColor));

            $box->getActiveParagraph()
                ->setSpacingAfter(25)
                ->getAlignment()
                ->setIndent(-40)
                ->setMarginLeft(60);
        }
    }

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'description' => 'The slide title (e.g., "Agenda" or "Table of Contents")',
                ],
                'items' => [
                    'type' => 'array',
                    'description' => 'Array of agenda items',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'required' => ['title', 'items'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title and numbered agenda/table of contents items';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Agenda',
            'items' => [
                'Introduction and Welcome',
                'Company Overview',
                'Product Demonstration',
                'Q&A Session',
                'Next Steps',
            ],
        ];
    }
}
