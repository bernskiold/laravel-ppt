<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

trait WithPosition
{
    public float $x = 0;

    public float $y = 0;

    public function x(float $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function y(float $y): static
    {
        $this->y = $y;

        return $this;
    }

    public function position(float $x, float $y): static
    {
        $this->x($x);
        $this->y($y);

        return $this;
    }
}
