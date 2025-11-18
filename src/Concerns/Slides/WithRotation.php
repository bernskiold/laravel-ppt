<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

trait WithRotation
{
    protected int $rotation = 0;

    public function rotate(int $degrees = 0): static
    {
        $this->rotation = $degrees;

        return $this;
    }

    /**
     * Data schema for rotation properties.
     */
    protected static function dataSchemaWithRotation(): array
    {
        return [
            'properties' => [
                'rotation' => [
                    'type' => 'integer',
                    'description' => 'Rotation angle in degrees (0-360)',
                    'minimum' => 0,
                    'maximum' => 360,
                ],
            ],
            'required' => [],
        ];
    }
}
