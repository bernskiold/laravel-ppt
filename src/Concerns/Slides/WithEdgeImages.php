<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

trait WithEdgeImages
{
    protected ?string $bottomLeftImagePath = null;

    protected ?string $bottomRightImagePath = null;

    protected ?string $topRightImagePath = null;

    protected ?string $topLeftImagePath = null;

    protected ?string $bottomLeftImageUrl = null;

    protected ?string $bottomRightImageUrl = null;

    protected ?string $topRightImageUrl = null;

    protected ?string $topLeftImageUrl = null;

    protected array $bottomLeftImageDimensions = [];

    protected array $bottomRightImageDimensions = [];

    protected array $topRightImageDimensions = [];

    protected array $topLeftImageDimensions = [];

    protected array $bottomLeftImagePosition = [];

    protected array $bottomRightImagePosition = [];

    protected array $topRightImagePosition = [];

    protected array $topLeftImagePosition = [];

    public function bottomLeftImage(string $path, int $width, int $height, ?int $x = null, ?int $y = null, ?string $url = null): self
    {
        if ($this->bottomLeftImagePath) {
            return $this;
        }

        $this->bottomLeftImagePath = $path;
        $this->bottomLeftImageDimensions = ['width' => $width, 'height' => $height];

        if ($x) {
            $this->bottomLeftImagePosition['x'] = $x;
        }

        if ($y) {
            $this->bottomLeftImagePosition['y'] = $y;
        }

        if ($url) {
            $this->bottomLeftImageUrl = $url;
        }

        return $this;
    }

    public function bottomRightImage(string $path, int $width, int $height, ?int $x = null, ?int $y = null, ?string $url = null): self
    {
        if ($this->bottomRightImagePath) {
            return $this;
        }

        $this->bottomRightImagePath = $path;
        $this->bottomRightImageDimensions = ['width' => $width, 'height' => $height];

        if ($x) {
            $this->bottomRightImagePosition['x'] = $x;
        }

        if ($y) {
            $this->bottomRightImagePosition['y'] = $y;
        }

        if ($url) {
            $this->bottomRightImageUrl = $url;
        }

        return $this;
    }

    public function topLeftImage(string $path, int $width, int $height, ?int $x = null, ?int $y = null, ?string $url = null): self
    {
        if ($this->topLeftImagePath) {
            return $this;
        }

        $this->topLeftImagePath = $path;
        $this->topLeftImageDimensions = ['width' => $width, 'height' => $height];

        if ($x) {
            $this->topLeftImagePosition['x'] = $x;
        }

        if ($y) {
            $this->topLeftImagePosition['y'] = $y;
        }

        if ($url) {
            $this->topLeftImageUrl = $url;
        }

        return $this;
    }

    public function topRightImage(string $path, int $width, int $height, ?int $x = null, ?int $y = null, ?string $url = null): self
    {
        if ($this->topRightImagePath) {
            return $this;
        }

        $this->topRightImagePath = $path;
        $this->topRightImageDimensions = ['width' => $width, 'height' => $height];

        if ($x) {
            $this->topRightImagePosition['x'] = $x;
        }

        if ($y) {
            $this->topRightImagePosition['y'] = $y;
        }

        if ($url) {
            $this->topRightImageUrl = $url;
        }

        return $this;
    }
}
