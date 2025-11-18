<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

trait WithChartBackground
{
    public ?string $chartBackgroundColor = null;

    public ?string $chartAxisColor = null;

    public function chartBackgroundColor(?string $argb): static
    {
        $this->chartBackgroundColor = $argb;

        return $this;
    }

    public function chartAxisColor(?string $argb): static
    {
        $this->chartAxisColor = $argb;

        return $this;
    }
}
