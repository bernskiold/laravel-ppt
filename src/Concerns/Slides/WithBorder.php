<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Outline;

trait WithBorder
{
    public ?string $borderColor = null;

    public string $borderType = Fill::FILL_SOLID;

    public float $borderSize = 1.0;

    public function border(string $color, float $size = 1.0, string $type = Fill::FILL_SOLID): static
    {
        return $this->borderSize($size)
            ->borderType($type)
            ->borderColor($color);
    }

    public function borderSize(int $size): static
    {
        $this->borderSize = $size;

        return $this;
    }

    public function borderType(string $type): static
    {
        $this->borderType = $type;

        return $this;
    }

    public function borderColor(string $color): static
    {
        $this->borderColor = $color;

        return $this;
    }

    protected function getBorderAsOutline(): Outline
    {
        return (new Outline)
            ->setWidth($this->borderSize)
            ->setFill(
                (new Fill)->setFillType($this->borderType)
                    ->setStartColor(new Color($this->borderColor))
                    ->setEndColor(new Color($this->borderColor))
            );
    }
}
