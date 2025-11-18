<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

trait WithBackgroundColor
{
    public ?string $backgroundColor = null;

    public function backgroundColor(string $argb): static
    {
        $this->backgroundColor = $argb;

        return $this;
    }

    public function getBackgroundColorFill(): Fill
    {
        return (new Fill)
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color($this->backgroundColor))
            ->setEndColor(new Color($this->backgroundColor));
    }
}
