<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\Slides\WithAlignment;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithFontSettings;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithShape;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Font;

/**
 * A text box of paragraphs, each made of runs with formatting of their own —
 * a bold name ahead of plain text — and optionally set as a bulleted list.
 *
 * What {@see TextBox} (one run) and {@see BulletPointBox} (plain strings) cannot
 * say. A paragraph is a string or a list of {@see TextPart}s; every run falls
 * back to the box's size, colour and font.
 *
 *     RichTextBox::make($slide, [
 *         [TextPart::make('Lisa Materazzo')->bold(), TextPart::make(', CMO, left the company.')],
 *         'Pete Zillig is on leave.',
 *     ])->bulleted()->size(10)->position(40, 120)->width(400)->height(200)->render();
 *
 * @method static static make(BaseSlide $slide, array $paragraphs = [])
 */
class RichTextBox extends Component
{
    use WithAlignment,
        WithFontSettings,
        WithShape;

    protected bool $bulleted = false;

    protected string $bulletCharacter = '•';

    protected ?string $bulletColor = null;

    protected int $spacingAfter = 0;

    /**
     * @param  array<int, string|array<int, TextPart>>  $paragraphs
     */
    public function __construct(
        protected array $paragraphs = [],
    ) {}

    protected function initialize(): static
    {
        $this->shape = $this->slide->raw()->createRichTextShape();

        return $this;
    }

    public function bulleted(bool $bulleted = true, string $character = '•'): static
    {
        $this->bulleted = $bulleted;
        $this->bulletCharacter = $character;

        return $this;
    }

    public function bulletColor(string $color): static
    {
        $this->bulletColor = $color;

        return $this;
    }

    public function spacingAfter(int $points): static
    {
        $this->spacingAfter = $points;

        return $this;
    }

    public function render(): static
    {
        $this->maybeDefaultDimensions();

        $color = $this->color ?? $this->slide->textColor;
        $font = $this->font ?? $this->slide->presentation->branding->baseFont();

        $this->shape->setWidth($this->width)
            ->setHeight($this->height)
            ->setOffsetX($this->x)
            ->setOffsetY($this->y);

        foreach (array_values($this->paragraphs) as $index => $paragraph) {
            $current = $index === 0
                ? $this->shape->getActiveParagraph()
                : $this->shape->createParagraph();

            $parts = is_string($paragraph) ? [TextPart::make($paragraph)] : $paragraph;

            foreach ($parts as $part) {
                $current->createTextRun($part->text())
                    ->getFont()
                    ->setName($part->font ?? $font)
                    ->setSize($part->size ?? $this->size ?? 12)
                    ->setColor(new Color($part->color ?? $color))
                    ->setBold($part->bold || $this->bold)
                    ->setItalic($part->italic || $this->italic)
                    ->setUnderline($part->underlined || $this->underlined ? Font::UNDERLINE_SINGLE : Font::UNDERLINE_NONE);
            }

            $current->setSpacingAfter($this->spacingAfter)
                ->getAlignment()
                ->setHorizontal($this->horizontalAlignment)
                ->setVertical($this->verticalAlignment);

            if ($this->bulleted) {
                $current->getBulletStyle()
                    ->setBulletType(Bullet::TYPE_BULLET)
                    ->setBulletChar($this->bulletCharacter)
                    ->setBulletColor(new Color($this->bulletColor ?? $color));

                $current->getAlignment()
                    ->setIndent(-14)
                    ->setMarginLeft(14);
            }
        }

        return $this;
    }
}
