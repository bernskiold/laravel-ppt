<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;

/**
 * @method static static make(string $title, array $bulletPoints = [])
 */
class BulletPoints extends BaseSlide implements DynamicallyCreatable
{
    use WithSlideTitle;

    public function __construct(
        string $title,
        protected array $bulletPoints = [],
    ) {
        $this->title($title);
    }

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

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'description' => 'The slide title',
                ],
                'bulletPoints' => [
                    'type' => 'array',
                    'description' => 'Array of bullet point text items',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'required' => ['title'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title and bullet points';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Key Features',
            'bulletPoints' => [
                'Easy to use interface',
                'Powerful analytics',
                'Real-time collaboration',
            ],
        ];
    }
}
