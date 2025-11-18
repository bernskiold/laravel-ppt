<?php

namespace BernskioldMedia\LaravelPpt\Branding;

use BernskioldMedia\LaravelPpt\Concerns\Makeable;
use Illuminate\Support\Str;
use PhpOffice\PhpPresentation\Style\Color;
use ReflectionClass;

use function array_merge;
use function collect;
use function config;

class Branding
{
    use Makeable;

    /**
     * The creator name.
     */
    protected string $creatorCompanyName;

    /**
     * The creator website URL.
     */
    protected string $websiteUrl;

    /**
     * The default font used for slide components.
     */
    protected string $baseFont;

    /**
     * A list of chart colors in ARGB format.
     * Will be merged with the default colors, where the
     * branding colors will take precedence.
     */
    protected array $chartColors = [];

    /**
     * Default chart colors for all branding.
     * This is loaded from the config file.
     */
    protected array $defaultChartColors = [];

    public function __construct()
    {
        if (empty($this->baseFont)) {
            $this->baseFont = config('powerpoint.baseBranding.font', 'Calibri');
        }

        if (empty($this->creatorCompanyName)) {
            $this->creatorCompanyName = config('powerpoint.baseBranding.creatorCompanyName', '');
        }

        if (empty($this->websiteUrl)) {
            $this->websiteUrl = config('powerpoint.baseBranding.websiteUrl', '#');
        }

        if (empty($this->chartColors)) {
            $this->defaultChartColors = config('powerpoint.baseBranding.chartColors', 'ff000000');
        }
    }

    /**
     * The creator website URL.
     */
    public function url(): string
    {
        return $this->websiteUrl;
    }

    /**
     * The creator name.
     */
    public function creatorCompanyName(): string
    {
        return $this->creatorCompanyName;
    }

    /**
     * The default font used for slide components.
     */
    public function baseFont(): string
    {
        return $this->baseFont;
    }

    /**
     * The path to the folder containing the branding assets.
     */
    public function assetFolderPath(): string
    {
        return config('powerpoint.paths.branding').'/'.$this->key();
    }

    /**
     * The key used to identify the branding.
     */
    public function key(): string
    {
        return Str::kebab((new ReflectionClass($this))->getShortName());
    }

    /**
     * Get a human-readable label for the branding.
     */
    public static function label(): string
    {
        return str(static::class)
            ->classBasename()
            ->snake(' ')
            ->title()
            ->toString();
    }

    /**
     * The default theme for all brandings.
     * Override this method in your custom branding class to add a logo.
     */
    public function defaultTheme(): SlideTheme
    {
        return SlideTheme::make()
            ->backgroundColor(Color::COLOR_WHITE)
            ->chartBackgroundColor(Color::COLOR_WHITE)
            ->textColor(Color::COLOR_BLACK);
    }

    /**
     * Customize the theme for a slide master.
     */
    public function slideTheme(?string $slideClass = null): SlideTheme
    {
        return $this->defaultTheme();
    }

    /**
     * Paragraph styles for this branding instance.
     * Override this method to customize the paragraph styles.
     * The styles returned here will be merged with the default styles.
     *
     * @return ParagraphStyle[]
     */
    protected function paragraphStyles(): array
    {
        return [];
    }

    /**
     * Default paragraph styles for all brandings.
     *
     * @return ParagraphStyle[]
     */
    protected function defaultParagraphStyles(): array
    {
        return [
            ParagraphStyle::make('slideTitle')
                ->size(24)
                ->bold(),
            ParagraphStyle::make('slideSubtitle')
                ->size(18)
                ->bold(),
            ParagraphStyle::make('bulletPoint')
                ->size(18),
            ParagraphStyle::make('nUpGridTitle')
                ->size(16)
                ->bold(),
            ParagraphStyle::make('nUpGridBody')
                ->size(12),
            ParagraphStyle::make('body')
                ->size(12),
            ParagraphStyle::make('sectionTitle')
                ->size(36)
                ->bold(),
            ParagraphStyle::make('sectionSubtitle')
                ->size(18)
                ->bold(),
        ];
    }

    /**
     * Get a paragraph style by key.
     */
    public function paragraphStyle(string $key): ?ParagraphStyle
    {
        $style = collect($this->paragraphStyles())
            ->filter(fn (ParagraphStyle $style) => $style->key === $key)
            ->first();

        if ($style) {
            return $style;
        }

        return collect($this->defaultParagraphStyles())
            ->filter(fn (ParagraphStyle $style) => $style->key === $key)
            ->first();
    }

    /**
     * Get the value of a paragraph style's property.
     */
    public function paragraphStyleValue(string $styleKey, string $property, mixed $default = null): mixed
    {
        $style = $this->paragraphStyle($styleKey);

        if (! $style) {
            return $default;
        }

        return $style->{$property} ?? $default;
    }

    /**
     * Get a chart color by its ID.
     */
    public function chartColor(int $id, bool $asObject = true): string|Color
    {
        $argb = $this->chartColors()[$id] ?? config('powerpoint.defaults.charts.seriesColor', 'ff000000');

        if ($asObject) {
            return new Color($argb);
        }

        return $argb;
    }

    /**
     * Get all available chart colors for this branding.
     */
    public function chartColors(): array
    {
        return array_merge(
            $this->defaultChartColors,
            $this->chartColors
        );
    }
}
