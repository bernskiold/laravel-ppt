<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\HasDataSchema;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithAlignment;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatableComponent;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;

/**
 * @method static static make(BaseSlide $slide, string $text)
 */
class BulletPointBox extends Component implements DynamicallyCreatableComponent
{
    use HasDataSchema;
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

    public static function key(): string
    {
        return 'bullet-point-box';
    }

    public static function description(): string
    {
        return 'A component for displaying bulleted lists';
    }

    public static function dataSchema(): array
    {
        return static::buildDataSchema([
            'properties' => [
                'bulletPoints' => [
                    'type' => 'array',
                    'description' => 'Array of bullet point text items',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
                'paragraphStyle' => [
                    'type' => 'string',
                    'description' => 'Named paragraph style from branding',
                ],
                'bulletCharacter' => [
                    'type' => 'string',
                    'description' => 'Character to use for bullets (e.g., "•", "-", ">")',
                ],
                'bulletColor' => [
                    'type' => 'string',
                    'description' => 'Bullet color as hex code',
                    'pattern' => '^[0-9A-Fa-f]{6}$',
                ],
                'spacingAfter' => [
                    'type' => 'integer',
                    'description' => 'Spacing after each bullet point in pixels',
                    'minimum' => 0,
                ],
            ],
            'required' => ['bulletPoints'],
        ]);
    }

    public static function exampleData(): array
    {
        return [
            'bulletPoints' => [
                'First point',
                'Second point',
                'Third point',
            ],
            'x' => 100,
            'y' => 150,
            'width' => 800,
            'height' => 300,
            'horizontalAlignment' => 'left',
        ];
    }

    public static function fromData(BaseSlide $slide, array $data): static
    {
        $bulletPoints = $data['bulletPoints'] ?? [];
        $component = static::make($slide, $bulletPoints);

        // Apply position
        if (isset($data['x']) && isset($data['y'])) {
            $component->position($data['x'], $data['y']);
        } elseif (isset($data['x'])) {
            $component->x($data['x']);
        } elseif (isset($data['y'])) {
            $component->y($data['y']);
        }

        // Apply size
        if (isset($data['width'])) {
            $component->width($data['width']);
        }
        if (isset($data['height'])) {
            $component->height($data['height']);
        }

        // Apply alignment
        if (isset($data['horizontalAlignment'])) {
            $component->horizontalAlignment($data['horizontalAlignment']);
        }
        if (isset($data['verticalAlignment'])) {
            $component->verticalAlignment($data['verticalAlignment']);
        }

        // Apply bullet styling
        if (isset($data['paragraphStyle'])) {
            $component->paragraphStyle($data['paragraphStyle']);
        }
        if (isset($data['bulletCharacter'])) {
            $component->bulletCharacter($data['bulletCharacter']);
        }
        if (isset($data['bulletColor'])) {
            $component->bulletColor($data['bulletColor']);
        }
        if (isset($data['spacingAfter'])) {
            $component->spacingAfter($data['spacingAfter']);
        }

        return $component;
    }
}
