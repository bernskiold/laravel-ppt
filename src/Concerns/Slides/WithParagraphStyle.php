<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

trait WithParagraphStyle
{
    public ?string $paragraphStyle = null;

    public function paragraphStyle(string $paragraphStyle): static
    {
        $this->paragraphStyle = $paragraphStyle;

        return $this;
    }

    /**
     * Data schema for paragraph style properties.
     */
    protected static function dataSchemaWithParagraphStyle(): array
    {
        return [
            'properties' => [
                'paragraphStyle' => [
                    'type' => 'string',
                    'description' => 'Named paragraph style from the presentation branding',
                ],
            ],
            'required' => [],
        ];
    }
}
