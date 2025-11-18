<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

trait WithFontSettings
{
    public ?int $size = null;

    public ?string $color = null;

    public float $letterSpacing = 0;

    public ?string $font = null;

    public bool $bold = false;

    public bool $underlined = false;

    public int $lineHeight = 100;

    public bool $uppercase = false;

    public function size(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function color(?string $color = null): static
    {
        $this->color = $color;

        return $this;
    }

    public function uppercase(bool $uppercase = true): static
    {
        $this->uppercase = $uppercase;

        return $this;
    }

    public function bold(bool $bold = true): static
    {
        $this->bold = $bold;

        return $this;
    }

    public function underlined(bool $underlined = true): static
    {
        $this->underlined = $underlined;

        return $this;
    }

    public function letterSpacing(float $letterSpacing = 5): static
    {
        $this->letterSpacing = $letterSpacing;

        return $this;
    }

    public function font(string $font): static
    {
        $this->font = $font;

        return $this;
    }

    public function lineHeight(int $lineHeight): static
    {
        $this->lineHeight = $lineHeight;

        return $this;
    }

    /**
     * Data schema for font settings properties.
     */
    protected static function dataSchemaWithFontSettings(): array
    {
        return [
            'properties' => [
                'fontSize' => [
                    'type' => 'integer',
                    'description' => 'Font size in points',
                    'minimum' => 1,
                ],
                'color' => [
                    'type' => 'string',
                    'description' => 'Text color as hex code (e.g., "FF0000" for red)',
                    'pattern' => '^[0-9A-Fa-f]{6}$',
                ],
                'font' => [
                    'type' => 'string',
                    'description' => 'Font family name',
                ],
                'bold' => [
                    'type' => 'boolean',
                    'description' => 'Whether the text should be bold',
                ],
                'underlined' => [
                    'type' => 'boolean',
                    'description' => 'Whether the text should be underlined',
                ],
                'uppercase' => [
                    'type' => 'boolean',
                    'description' => 'Whether the text should be converted to uppercase',
                ],
                'letterSpacing' => [
                    'type' => 'number',
                    'description' => 'Letter spacing in points',
                ],
                'lineHeight' => [
                    'type' => 'integer',
                    'description' => 'Line height as percentage (e.g., 100 for normal)',
                    'minimum' => 0,
                ],
            ],
            'required' => [],
        ];
    }
}
