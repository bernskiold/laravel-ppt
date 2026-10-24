<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

trait WithSize
{
    public ?int $width = null;

    public ?int $height = null;

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }
}
