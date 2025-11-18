<?php

namespace BernskioldMedia\LaravelPpt\Branding;

use BernskioldMedia\LaravelPpt\Components\Component;
use BernskioldMedia\LaravelPpt\Concerns\Makeable;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithFontSettings;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;

use function method_exists;

/**
 * @method static static make(string $key)
 */
class ParagraphStyle implements Arrayable
{
    use Conditionable,
        Makeable,
        WithFontSettings;

    public function __construct(
        public string $key
    ) {}

    public function toArray()
    {
        return [
            'key' => $this->key,
            'settings' => [
                'size' => $this->size,
                'color' => $this->color,
                'letterSpacing' => $this->letterSpacing,
                'font' => $this->font,
                'bold' => $this->bold,
                'underlined' => $this->underlined,
                'lineHeight' => $this->lineHeight,
                'uppercase' => $this->uppercase,
            ],
        ];
    }

    /**
     * Apply the style to a component.
     */
    public function applyToComponent(Component $component): void
    {
        if ($this->size && method_exists($component, 'size') && $component->size === null) {
            $component->size($this->size);
        }

        if ($this->color && method_exists($component, 'color') && $component->color === null) {
            $component->color($this->color);
        }

        if ($this->letterSpacing && method_exists($component, 'letterSpacing') && $component->letterSpacing === null) {
            $component->letterSpacing($this->letterSpacing);
        }

        if ($this->font && method_exists($component, 'font') && $component->font === null) {
            $component->font($this->font);
        }

        if ($this->bold && method_exists($component, 'bold')) {
            $component->bold($this->bold);
        }

        if ($this->underlined && method_exists($component, 'underlined') && $component->underlined === null) {
            $component->underlined($this->underlined);
        }

        if ($this->lineHeight && method_exists($component, 'lineHeight') && $component->lineHeight === null) {
            $component->lineHeight($this->lineHeight);
        }

        if ($this->uppercase && method_exists($component, 'uppercase')) {
            $component->uppercase($this->uppercase);
        }
    }
}
