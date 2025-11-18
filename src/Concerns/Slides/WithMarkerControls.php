<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

trait WithMarkerControls
{
    public bool $showMarker = true;

    public function withMarkers(bool $show = true): static
    {
        $this->showMarker = $show;

        return $this;
    }

    public function withoutMarkers(bool $hide = true): static
    {
        return $this->withMarkers(! $hide);
    }
}
