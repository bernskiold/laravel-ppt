<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;

/**
 * @method static static make(string $title, array $bulletPoints = [])
 */
class BulletPoints extends BaseSlide
{
    use WithSlideTitle;

    public function __construct(
        string $title,
        protected array $bulletPoints = [],
    ) {}

    public function bullet(string $text): self
    {
        $this->bulletPoints[] = $text;

        return $this;
    }

    protected function render(): void
    {
        $titleBox = $this->renderTitle();

        $yOffset = $titleBox->height + 75;
        $box = null;

        foreach ($this->bulletPoints as $bulletPoint) {
            if (! $box) {
                $box = TextBox::make($this, $bulletPoint)
                    ->paragraphStyle('bulletPoint')
                    ->height($this->presentation->height - $titleBox->height - 200)
                    ->width($this->presentation->width - (2 * $this->horizontalPadding))
                    ->position($this->horizontalPadding, $yOffset)
                    ->alignLeft()
                    ->alignTop()
                    ->render()
                    ->shape;
            } else {
                $box->createParagraph()
                    ->createTextRun($bulletPoint)
                    ->getFont()
                    ->setSize($this->presentation->branding->paragraphStyleValue('bulletPoint', 'size'))
                    ->setColor(new Color($this->textColor))
                    ->setBold($this->presentation->branding->paragraphStyleValue('bulletPoint', 'bold'))
                    ->setName($this->presentation->branding->paragraphStyleValue('bulletPoint', 'font') ?? $this->presentation->branding->baseFont())
                    ->setCharacterSpacing($this->presentation->branding->paragraphStyleValue('bulletPoint', 'letterSpacing') ?? 0);
            }

            $box->getActiveParagraph()
                ->getBulletStyle()
                ->setBulletType(Bullet::TYPE_BULLET)
                ->setBulletChar('•')
                ->setBulletColor(new Color($this->textColor));

            $box->getActiveParagraph()
                ->setSpacingAfter(20)
                ->getAlignment()
                ->setIndent(-40)
                ->setMarginLeft(40);
        }
    }
}
