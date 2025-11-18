<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

use PhpOffice\PhpPresentation\Style\Borders;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

trait WithBorders
{
    public ?string $topBorderColor = Color::COLOR_BLACK;

    public string $topBorderType = Fill::FILL_SOLID;

    public float $topBorderSize = 0;

    public ?string $rightBorderColor = Color::COLOR_BLACK;

    public string $rightBorderType = Fill::FILL_SOLID;

    public float $rightBorderSize = 0;

    public ?string $bottomBorderColor = Color::COLOR_BLACK;

    public string $bottomBorderType = Fill::FILL_SOLID;

    public float $bottomBorderSize = 0;

    public ?string $leftBorderColor = Color::COLOR_BLACK;

    public string $leftBorderType = Fill::FILL_SOLID;

    public float $leftBorderSize = 0;

    public function borderTop(string $color, float $size = 1.0, string $type = Fill::FILL_SOLID): static
    {
        $this->topBorderColor = $color;
        $this->topBorderType = $type;
        $this->topBorderSize = $size;

        return $this;
    }

    public function borderRight(string $color, float $size = 1.0, string $type = Fill::FILL_SOLID): static
    {
        $this->rightBorderColor = $color;
        $this->rightBorderType = $type;
        $this->rightBorderSize = $size;

        return $this;
    }

    public function borderBottom(string $color, float $size = 1.0, string $type = Fill::FILL_SOLID): static
    {
        $this->bottomBorderColor = $color;
        $this->bottomBorderType = $type;
        $this->bottomBorderSize = $size;

        return $this;
    }

    public function borderLeft(string $color, float $size = 1.0, string $type = Fill::FILL_SOLID): static
    {
        $this->leftBorderColor = $color;
        $this->leftBorderType = $type;
        $this->leftBorderSize = $size;

        return $this;
    }

    public function getBordersObject(): Borders
    {
        $borders = new Borders;

        $borders->getTop()
            ->setLineWidth($this->topBorderSize)
            ->setColor(new Color($this->topBorderColor));

        $borders->getRight()
            ->setLineWidth($this->rightBorderSize)
            ->setColor(new Color($this->rightBorderColor));

        $borders->getBottom()
            ->setLineWidth($this->bottomBorderSize)
            ->setColor(new Color($this->bottomBorderColor));

        $borders->getLeft()
            ->setLineWidth($this->leftBorderSize)
            ->setColor(new Color($this->leftBorderColor));

        return $borders;
    }
}
