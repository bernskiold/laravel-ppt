<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

use PhpOffice\PhpPresentation\Style\Alignment;

trait WithAlignment
{
    public string $horizontalAlignment = Alignment::HORIZONTAL_CENTER;

    public string $verticalAlignment = Alignment::VERTICAL_CENTER;

    public float $marginTop = 0;

    public float $marginRight = 0;

    public float $marginBottom = 0;

    public float $marginLeft = 0;

    public function alignLeft(): static
    {
        return $this->horizontalAlignment(Alignment::HORIZONTAL_LEFT);
    }

    public function alignCenter(): static
    {
        return $this->horizontalAlignment(Alignment::HORIZONTAL_CENTER);
    }

    public function alignRight(): static
    {
        return $this->horizontalAlignment(Alignment::HORIZONTAL_RIGHT);
    }

    public function alignTop(): static
    {
        return $this->verticalAlignment(Alignment::VERTICAL_TOP);
    }

    public function alignMiddle(): static
    {
        return $this->verticalAlignment(Alignment::VERTICAL_CENTER);
    }

    public function alignBottom(): static
    {
        return $this->verticalAlignment(Alignment::VERTICAL_BOTTOM);
    }

    public function marginTop(float $margin): static
    {
        $this->marginTop = $margin;

        return $this;
    }

    public function marginRight(float $margin): static
    {
        $this->marginRight = $margin;

        return $this;
    }

    public function marginBottom(float $margin): static
    {
        $this->marginBottom = $margin;

        return $this;
    }

    public function marginLeft(float $margin): static
    {
        $this->marginLeft = $margin;

        return $this;
    }

    public function margin(float $top, ?float $right = null, ?float $bottom = null, ?float $left = null): static
    {
        $this->marginTop = $top;
        $this->marginRight = $right ?? $top;
        $this->marginBottom = $bottom ?? $top;
        $this->marginLeft = $left ?? $right ?? $top;

        return $this;
    }

    public function horizontalAlignment(string $horizontalAlignment): static
    {
        $this->horizontalAlignment = $horizontalAlignment;

        return $this;
    }

    public function verticalAlignment(string $verticalAlignment): static
    {
        $this->verticalAlignment = $verticalAlignment;

        return $this;
    }

    /**
     * Data schema for alignment properties.
     */
    protected static function dataSchemaWithAlignment(): array
    {
        return [
            'properties' => [
                'horizontalAlignment' => [
                    'type' => 'string',
                    'description' => 'Horizontal alignment',
                    'enum' => ['left', 'center', 'right'],
                ],
                'verticalAlignment' => [
                    'type' => 'string',
                    'description' => 'Vertical alignment',
                    'enum' => ['top', 'center', 'bottom'],
                ],
                'marginTop' => [
                    'type' => 'number',
                    'description' => 'Top margin in pixels',
                    'minimum' => 0,
                ],
                'marginRight' => [
                    'type' => 'number',
                    'description' => 'Right margin in pixels',
                    'minimum' => 0,
                ],
                'marginBottom' => [
                    'type' => 'number',
                    'description' => 'Bottom margin in pixels',
                    'minimum' => 0,
                ],
                'marginLeft' => [
                    'type' => 'number',
                    'description' => 'Left margin in pixels',
                    'minimum' => 0,
                ],
            ],
            'required' => [],
        ];
    }
}
