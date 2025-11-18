<?php

namespace BernskioldMedia\LaravelPpt\Branding;

use BernskioldMedia\LaravelPpt\Concerns\Makeable;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBackgroundColor;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBackgroundImage;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithChartBackground;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithLogo;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithTextColor;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use Illuminate\Support\Traits\Conditionable;

/**
 * @method static self make(?self $copyFrom = null)
 */
class SlideTheme
{
    use Conditionable,
        Makeable,
        WithBackgroundColor,
        WithBackgroundImage,
        WithChartBackground,
        WithLogo,
        WithTextColor;

    /**
     * The class name of the custom master slide to use.
     */
    public ?string $customMasterSlide = null;

    public function __construct(?self $copyFrom = null)
    {
        if ($copyFrom) {
            $this->copyFromTheme($copyFrom);
        }
    }

    /**
     * Use a custom master slide for this theme.
     */
    public function customMasterSlide(string $className): self
    {
        $this->customMasterSlide = $className;

        return $this;
    }

    /**
     * Apply the theme to a slide.
     */
    public function applyToSlide(BaseSlide $slide): void
    {
        if ($this->backgroundColor && $slide->backgroundColor === null) {
            $slide->backgroundColor($this->backgroundColor);
        }

        if ($this->backgroundImage && $slide->backgroundImage === null) {
            $slide->backgroundImage($this->backgroundImage, true);
        }

        if ($this->chartBackgroundColor && $slide->chartBackgroundColor === null) {
            $slide->chartBackgroundColor($this->chartBackgroundColor);
        }

        if ($this->chartAxisColor && $slide->chartAxisColor === null) {
            $slide->chartAxisColor($this->chartAxisColor);
        }

        if ($this->textColor && ! $slide->textColor) {
            $slide->textColor($this->textColor);
        }

        if ($this->logo && ! $slide->logo) {
            $slide->logo($this->logo, $this->logoDimensions, $this->logoPosition);
        }
    }

    /**
     * Copy the settings from another SlideTheme instance.
     */
    public function copyFromTheme(self $theme): self
    {
        $this->backgroundColor = $theme->backgroundColor;
        $this->backgroundImage = $theme->backgroundImage;
        $this->chartBackgroundColor = $theme->chartBackgroundColor;
        $this->chartAxisColor = $theme->chartAxisColor;
        $this->textColor = $theme->textColor;
        $this->logo = $theme->logo;
        $this->logoDimensions = $theme->logoDimensions;
        $this->logoPosition = $theme->logoPosition;
        $this->customMasterSlide = $theme->customMasterSlide;

        return $this;
    }
}
