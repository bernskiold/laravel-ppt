<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\HasDataSchema;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithAlignment;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBackgroundColor;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithFontSettings;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithParagraphStyle;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithRotation;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithShape;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithUrl;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatableComponent;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Shape\RichText\Run;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Font;

/**
 * @method static TextBox make(BaseSlide $slide, string $text = null)
 */
class TextBox extends Component implements DynamicallyCreatableComponent
{
    use HasDataSchema;
    use WithAlignment,
        WithBackgroundColor,
        WithFontSettings,
        WithParagraphStyle,
        WithRotation,
        WithShape,
        WithUrl;

    public ?Run $textRun = null;

    public int $lines = 2;

    public function __construct(
        protected ?string $text = '',
    ) {}

    protected function initialize(): static
    {
        $this->shape = $this->slide->raw()->createRichTextShape();

        return $this;
    }

    public function render(): static
    {
        // Don't render if there is no text.
        if (empty($this->text)) {
            return $this;
        }

        // Apply the paragraph style if it exists.
        if (! empty($this->paragraphStyle)) {
            $this->slide
                ->presentation
                ->branding
                ->paragraphStyle($this->paragraphStyle)
                ?->applyToComponent($this);
        }

        $this->shape->getActiveParagraph()
            ->setLineSpacing($this->lineHeight)
            ->getAlignment()
            ->setHorizontal($this->horizontalAlignment)
            ->setVertical($this->verticalAlignment);

        // Calculate a default height based on how many lines we have asked for.
        if (! $this->height) {
            $this->height = ($this->size ?? 12) * $this->lines;
        }

        if (! $this->width) {
            $this->width = $this->slide->presentation->width - $this->x * 2;
        }

        $this->shape->setWidth($this->width)
            ->setHeight($this->height)
            ->setOffsetX($this->x)
            ->setOffsetY($this->y)
            ->setRotation($this->rotation);

        if ($this->backgroundColor) {
            $this->shape->setFill($this->getBackgroundColorFill());
        }

        if (! $this->color) {
            $this->color = $this->slide->textColor;
        }

        if ($this->uppercase) {
            $this->text = strtoupper($this->text);
        }

        $this->textRun = $this->shape->createTextRun($this->text);

        $this->textRun->getFont()
            ->setName($this->font ?? $this->slide->presentation->branding->baseFont())
            ->setSize($this->size ?? 12)
            ->setBold($this->bold)
            ->setUnderline($this->underlined ? Font::UNDERLINE_SINGLE : Font::UNDERLINE_NONE)
            ->setCharacterSpacing($this->letterSpacing)
            ->setColor(new Color($this->color));

        if ($this->url || $this->slideNumberAnchor) {
            $this->textRun->setHyperlink(
                $this->getLinkAsHyperlink()
            );
        }

        return $this;
    }

    protected function defaultHeight(): float
    {
        return ($this->size ?? 12) * $this->lines;
    }

    public function lines(int $lines): static
    {
        $this->lines = $lines;

        return $this;
    }

    /**
     * Get the registry key for this component.
     */
    public static function key(): string
    {
        return 'text-box';
    }

    /**
     * Get a human-readable description of this component.
     */
    public static function description(): string
    {
        return 'A text box for displaying formatted text content';
    }

    /**
     * Get the JSON schema for this component's data requirements.
     *
     * The schema is automatically merged with schemas from traits.
     */
    public static function dataSchema(): array
    {
        return static::buildDataSchema([
            'properties' => [
                'text' => [
                    'type' => 'string',
                    'description' => 'The text content to display',
                ],
                'lines' => [
                    'type' => 'integer',
                    'description' => 'Number of lines (used for height calculation if height not specified)',
                    'minimum' => 1,
                ],
            ],
            'required' => ['text'],
        ]);
    }

    /**
     * Get an example data structure for this component.
     */
    public static function exampleData(): array
    {
        return [
            'text' => 'Hello World',
            'x' => 100,
            'y' => 100,
            'width' => 400,
            'height' => 100,
            'fontSize' => 24,
            'bold' => true,
            'color' => '2c3e50',
            'horizontalAlignment' => 'center',
            'verticalAlignment' => 'center',
        ];
    }

    /**
     * Create an instance of the component from a data array.
     *
     * @param  BaseSlide  $slide  The slide to render the component on
     * @param  array  $data  The data array containing all component parameters
     */
    public static function fromData(BaseSlide $slide, array $data): static
    {
        $text = $data['text'] ?? '';
        $component = static::make($slide, $text);

        // Apply position if provided
        if (isset($data['x']) && isset($data['y'])) {
            $component->position($data['x'], $data['y']);
        } elseif (isset($data['x'])) {
            $component->x($data['x']);
        } elseif (isset($data['y'])) {
            $component->y($data['y']);
        }

        // Apply size if provided
        if (isset($data['width'])) {
            $component->width($data['width']);
        }
        if (isset($data['height'])) {
            $component->height($data['height']);
        }

        // Apply font settings
        if (isset($data['fontSize'])) {
            $component->size($data['fontSize']);
        }
        if (isset($data['color'])) {
            $component->color($data['color']);
        }
        if (isset($data['font'])) {
            $component->font($data['font']);
        }
        if (isset($data['bold'])) {
            $component->bold($data['bold']);
        }
        if (isset($data['underlined'])) {
            $component->underlined($data['underlined']);
        }
        if (isset($data['uppercase'])) {
            $component->uppercase($data['uppercase']);
        }
        if (isset($data['letterSpacing'])) {
            $component->letterSpacing($data['letterSpacing']);
        }
        if (isset($data['lineHeight'])) {
            $component->lineHeight($data['lineHeight']);
        }

        // Apply alignment
        if (isset($data['horizontalAlignment'])) {
            $component->horizontalAlignment($data['horizontalAlignment']);
        }
        if (isset($data['verticalAlignment'])) {
            $component->verticalAlignment($data['verticalAlignment']);
        }

        // Apply margins
        if (isset($data['marginTop'])) {
            $component->marginTop($data['marginTop']);
        }
        if (isset($data['marginRight'])) {
            $component->marginRight($data['marginRight']);
        }
        if (isset($data['marginBottom'])) {
            $component->marginBottom($data['marginBottom']);
        }
        if (isset($data['marginLeft'])) {
            $component->marginLeft($data['marginLeft']);
        }

        // Apply background color
        if (isset($data['backgroundColor'])) {
            $component->backgroundColor($data['backgroundColor']);
        }

        // Apply rotation
        if (isset($data['rotation'])) {
            $component->rotate($data['rotation']);
        }

        // Apply paragraph style
        if (isset($data['paragraphStyle'])) {
            $component->paragraphStyle($data['paragraphStyle']);
        }

        // Apply URL
        if (isset($data['url'])) {
            $component->url($data['url']);
        }
        if (isset($data['slideNumberAnchor'])) {
            $component->linkToSlide($data['slideNumberAnchor']);
        }

        // Apply lines
        if (isset($data['lines'])) {
            $component->lines($data['lines']);
        }

        return $component;
    }
}
