<?php

namespace BernskioldMedia\LaravelPpt\Presentation;

use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\Concerns\Makeable;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBackgroundColor;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBackgroundImage;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithChartBackground;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithDataSource;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithEdgeImages;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithLogo;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithPadding;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSize;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithTextColor;
use BernskioldMedia\LaravelPpt\Contracts\Slide as SlideContract;
use Closure;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Slide\Background\Color;

use function config;
use function tap;

abstract class BaseSlide implements SlideContract
{
    use Conditionable,
        Makeable,
        Tappable,
        WithBackgroundColor,
        WithBackgroundImage,
        WithChartBackground,
        WithDataSource,
        WithEdgeImages,
        WithLogo,
        WithPadding,
        WithSize,
        WithTextColor;

    public const EDGE_IMAGE_POSITION_TOP_LEFT = 'topLeft';

    public const EDGE_IMAGE_POSITION_TOP_RIGHT = 'topRight';

    public const EDGE_IMAGE_POSITION_BOTTOM_LEFT = 'bottomLeft';

    public const EDGE_IMAGE_POSITION_BOTTOM_RIGHT = 'bottomRight';

    public Presentation $presentation;

    public Slide $slide;

    public bool $withoutLogo = false;

    public string $footerText = '';

    protected ?Closure $modify = null;

    public function create(Presentation $presentation): Slide
    {
        $this->presentation = $presentation;
        $this->slide = $this->presentation->document->createSlide();

        $this->width = $this->presentation->width;
        $this->height = $this->presentation->height;

        $this->verticalPadding = $this->presentation->verticalPadding;
        $this->horizontalPadding = $this->presentation->horizontalPadding;

        // Apply slide theme.
        $theme = $this->presentation->branding->slideTheme(static::class);
        $theme->applyToSlide($this);

        // Apply slide-specific settings.
        $this->style();

        // Apply custom modifications.
        tap($this, $this->modify);

        // Render the contents.
        if ($theme->customMasterSlide) {
            ($theme->customMasterSlide)($this);
        } else {
            $this->render();
        }

        return $this->slide;
    }

    abstract protected function render(): void;

    protected function style(): void
    {
        $this->applyBackgroundColor();
        $this->applyBackgroundImage();
        $this->applyLogo();
        $this->applyDataSource();

        $this->applyEdgeImage('topRight');
        $this->applyEdgeImage('bottomRight');
        $this->applyEdgeImage('bottomLeft');
        $this->applyEdgeImage('topLeft');
    }

    public function modify(callable $function): static
    {
        $this->modify = $function;

        return $this;
    }

    public function withLogo(): self
    {
        $this->withoutLogo = false;

        return $this;
    }

    public function withoutLogo(): self
    {
        $this->withoutLogo = true;

        return $this;
    }

    public function withFooter(string $text): self
    {
        $this->footerText = $text;

        return $this;
    }

    protected function applyBackgroundColor(): void
    {
        $color = (new \PhpOffice\PhpPresentation\Style\Color($this->backgroundColor));
        $background = (new Color)->setColor($color);

        $this->slide->setBackground($background);
    }

    protected function applyBackgroundImage(): void
    {
        if ($this->backgroundImage === null) {
            return;
        }

        $background = (new Slide\Background\Image)->setPath($this->backgroundImage);
        $this->slide->setBackground($background);
    }

    protected function applyLogo(): void
    {
        if ($this->withoutLogo === true) {
            return;
        }

        if (! $this->logo) {
            return;
        }

        match ($this->logoPosition) {
            self::EDGE_IMAGE_POSITION_TOP_RIGHT => $this->topRightImage(
                $this->logo,
                $this->logoDimensions['width'],
                $this->logoDimensions['height']
            ),
            self::EDGE_IMAGE_POSITION_BOTTOM_RIGHT => $this->bottomRightImage(
                $this->logo,
                $this->logoDimensions['width'],
                $this->logoDimensions['height']
            ),
            self::EDGE_IMAGE_POSITION_BOTTOM_LEFT => $this->bottomLeftImage(
                $this->logo,
                $this->logoDimensions['width'],
                $this->logoDimensions['height']
            ),
            self::EDGE_IMAGE_POSITION_TOP_LEFT => $this->topLeftImage(
                $this->logo,
                $this->logoDimensions['width'],
                $this->logoDimensions['height']
            ),
        };
    }

    protected function applyDataSource(): void
    {
        if (! $this->showDataSource) {
            return;
        }

        if ($this->dataSourceMessage) {
            if (str_contains($this->dataSourceMessage, 'Source:')) {
                $text = $this->dataSourceMessage;
            } else {
                $text = __('Source: :application (:message)', [
                    'application' => config('powerpoint.defaults.presentation.dataSourceApplication', ''),
                    'message' => $this->dataSourceMessage,
                ]);
            }
        } else {
            $text = __('Source: :source', [
                'source' => config('powerpoint.defaults.presentation.dataSource', ''),
            ]);
        }

        TextBox::make($this, $text)
            ->color($this->textColor)
            ->alignRight()
            ->width(500)
            ->y($this->presentation->height - $this->presentation->verticalPadding)
            ->x($this->presentation->width - 500 - $this->presentation->horizontalPadding)
            ->bold(false)
            ->size(8)
            ->height($this->presentation->verticalPadding - 8)
            ->render();
    }

    protected function applyEdgeImage(string $key): void
    {
        $imagePath = $this->{"{$key}ImagePath"};
        $imageDimensions = $this->{"{$key}ImageDimensions"};
        $position = $this->{"{$key}ImagePosition"};
        $url = $this->{"{$key}ImageUrl"};

        if (! $imagePath) {
            return;
        }

        $x = $position['x'] ?? match ($key) {
            self::EDGE_IMAGE_POSITION_TOP_LEFT, self::EDGE_IMAGE_POSITION_BOTTOM_LEFT => $this->horizontalPadding,
            self::EDGE_IMAGE_POSITION_TOP_RIGHT, self::EDGE_IMAGE_POSITION_BOTTOM_RIGHT => $this->presentation->width - $imageDimensions['width'] - $this->horizontalPadding / 2,
        };

        $y = $position['y'] ?? match ($key) {
            self::EDGE_IMAGE_POSITION_TOP_LEFT, self::EDGE_IMAGE_POSITION_TOP_RIGHT => $this->verticalPadding,
            self::EDGE_IMAGE_POSITION_BOTTOM_LEFT, self::EDGE_IMAGE_POSITION_BOTTOM_RIGHT => $this->presentation->height - $imageDimensions['height'] - $this->verticalPadding / 2,
        };

        $shape = $this->slide->createDrawingShape();

        $shape->setPath($imagePath)
            ->setWidthAndHeight($imageDimensions['width'], $imageDimensions['height'])
            ->setOffsetX($x)
            ->setOffsetY($y)
            ->setName(str()->random());

        if ($url) {
            $shape->getHyperlink()->setUrl($url);
        }

    }

    public function raw(): Slide
    {
        return $this->slide;
    }
}
