<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\Slides\WithAlignment;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;

/**
 * @method static static make(BaseSlide $slide, string $text)
 */
class BulletPointBox extends Component
{
    use WithAlignment;

    protected string $paragraphStyle = 'bulletPoint';

    protected string $bulletCharacter = '•';

    protected ?string $bulletColor = null;

    protected int $spacingAfter = 20;

    public function __construct(
        protected array $bulletPoints = [],
    ) {}

    public function bullet(string $text): self
    {
        $this->bulletPoints[] = $text;

        return $this;
    }

    public function paragraphStyle(string $style): self
    {
        $this->paragraphStyle = $style;

        return $this;
    }

    public function bulletCharacter(string $bulletCharacter): self
    {
        $this->bulletCharacter = $bulletCharacter;

        return $this;
    }

    public function bulletColor(string $bulletColor): self
    {
        $this->bulletColor = $bulletColor;

        return $this;
    }

    public function spacingAfter(int $spacingAfter): self
    {
        $this->spacingAfter = $spacingAfter;

        return $this;
    }

    public function render(): static
    {
        $box = null;

        foreach ($this->bulletPoints as $bulletPoint) {
            if (! $box) {
                $box = TextBox::make($this->slide, $bulletPoint)
                    ->paragraphStyle($this->paragraphStyle)
                    ->height($this->height)
                    ->horizontalAlignment($this->horizontalAlignment)
                    ->verticalAlignment($this->verticalAlignment)
                    ->width($this->width)
                    ->position($this->x, $this->y)
                    ->render()
                    ->shape;
            } else {
                $box->createParagraph()
                    ->createTextRun($bulletPoint)
                    ->getFont()
                    ->setSize($this->slide->presentation->branding->paragraphStyleValue($this->paragraphStyle, 'size'))
                    ->setColor(new Color($this->slide->textColor))
                    ->setBold($this->slide->presentation->branding->paragraphStyleValue($this->paragraphStyle, 'bold'))
                    ->setName($this->slide->presentation->branding->paragraphStyleValue($this->paragraphStyle, 'font') ?? $this->slide->presentation->branding->baseFont())
                    ->setCharacterSpacing($this->slide->presentation->branding->paragraphStyleValue($this->paragraphStyle, 'letterSpacing') ?? 0);
            }

            $box->getActiveParagraph()
                ->getBulletStyle()
                ->setBulletType(Bullet::TYPE_BULLET)
                ->setBulletChar($this->bulletCharacter)
                ->setBulletColor(new Color($this->bulletColor ?? $this->slide->textColor));

            $box->getActiveParagraph()
                ->setSpacingAfter($this->spacingAfter)
                ->getAlignment()
                ->setIndent(-40)
                ->setMarginLeft(40);
        }

        return $this;
    }
}
